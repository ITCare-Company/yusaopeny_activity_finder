<?php

namespace Drupal\openy_activity_finder;

/**
 * Composes one or more Activity Finder backends into a single response.
 *
 * A block stores an ordered list of backend plugin ids (primary first). Every
 * backend exposes count, facets and a result slice separately
 * (OpenyActivityFinderBackendInterface). Composition (W0b DECISIONS D10/D11):
 *   - count: sum of each backend's cheap getResultsCount();
 *   - facets: merged per facet key, option counts summed;
 *   - results: the global page is routed across backends using their counts,
 *     so only the needed slice (<= one page of rows) is fetched — no full-set
 *     reads, no fallback. Backends appear in list order, primary first.
 *
 * This is the only place the runProgramSearch response shape is assembled.
 */
class ActivityFinderBackendAggregator {

  /**
   * The backend plugin manager.
   *
   * @var \Drupal\openy_activity_finder\ActivityFinderBackendManager
   */
  protected ActivityFinderBackendManager $backendManager;

  /**
   * Constructs the aggregator.
   *
   * @param \Drupal\openy_activity_finder\ActivityFinderBackendManager $backend_manager
   *   The backend plugin manager.
   */
  public function __construct(ActivityFinderBackendManager $backend_manager) {
    $this->backendManager = $backend_manager;
  }

  /**
   * Resolves plugin ids to backend instances, preserving order (weight).
   *
   * @param string[] $plugin_ids
   *   Backend plugin ids, primary first.
   *
   * @return \Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface[]
   *   Resolved instances, keyed by id. Unknown ids are skipped.
   */
  public function resolveBackends(array $plugin_ids): array {
    $backends = [];
    foreach ($plugin_ids as $id) {
      if ($this->backendManager->hasDefinition($id)) {
        $backends[$id] = $this->backendManager->createInstance($id);
      }
    }
    return $backends;
  }

  /**
   * Returns the primary backend (first in the list) for option lists.
   *
   * @param string[] $plugin_ids
   *   Backend plugin ids, primary first.
   *
   * @return \Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface|null
   *   The primary backend, or NULL when none resolve.
   */
  public function getPrimaryBackend(array $plugin_ids): ?OpenyActivityFinderBackendInterface {
    $backends = $this->resolveBackends($plugin_ids);
    return $backends ? reset($backends) : NULL;
  }

  /**
   * Runs a program search across the backends and assembles the response.
   *
   * @param string[] $plugin_ids
   *   Backend plugin ids, primary first.
   * @param array $parameters
   *   GET parameters for the search.
   * @param int $log_id
   *   Search log id.
   *
   * @return array
   *   The response (count, facets, pager, pager_info, table, groupedLocations,
   *   sort) or ['error' => ...] when no backend is available.
   */
  public function search(array $plugin_ids, array $parameters, int $log_id): array {
    $backends = $this->resolveBackends($plugin_ids);
    if (!$backends) {
      return ['error' => (string) t('Activity Finder is not available now.')];
    }

    $counts = [];
    $total = 0;
    foreach ($backends as $id => $backend) {
      $counts[$id] = $backend->getResultsCount($parameters);
      $total += $counts[$id];
    }

    $per_page = OpenyActivityFinderBackendInterface::RESULTS_PER_PAGE;
    $page = (int) ($parameters['page'] ?? 0);
    $offset = $page > 0 ? ($page - 1) * $per_page : 0;

    $primary = reset($backends);
    $facets = $this->mergeFacets($backends, $parameters);
    return [
      'count' => $total,
      'facets' => $facets,
      'pager' => ($page && $total > $per_page) ? $page : 0,
      'pager_info' => $this->pagerInfo($total, $per_page),
      'table' => $this->routeSlice($backends, $counts, $parameters, $offset, $per_page, $log_id),
      'groupedLocations' => $this->groupedLocations($primary, $facets),
      'sort' => $parameters['sort'] ?? 'title__ASC',
      'externals' => $this->collectExternals($backends, $parameters),
    ];
  }

  /**
   * Collects backend-specific extras keyed by plugin id (D10 'externals').
   */
  protected function collectExternals(array $backends, array $parameters): array {
    $externals = [];
    foreach ($backends as $id => $backend) {
      $extras = $backend->getExternals($parameters);
      if ($extras) {
        $externals[$id] = $extras;
      }
    }
    return $externals;
  }

  /**
   * Fetches the global [$offset, $offset + $limit) slice across backends.
   *
   * Walks backends in order; each covers a contiguous global range sized by its
   * count. Only backends overlapping the requested window are queried, and only
   * for their overlapping sub-slice. Rows are deduped by nid.
   */
  protected function routeSlice(array $backends, array $counts, array $parameters, int $offset, int $limit, int $log_id): array {
    $rows = [];
    $seen = [];
    $base = 0;
    foreach ($backends as $id => $backend) {
      if (count($rows) >= $limit) {
        break;
      }
      $count = $counts[$id];
      $window_start = max($offset, $base);
      $window_end = min($offset + $limit, $base + $count);
      if ($window_start < $window_end) {
        $local_offset = $window_start - $base;
        $local_limit = $window_end - $window_start;
        foreach ($backend->getResults($parameters, $local_offset, $local_limit, $log_id) as $row) {
          // Identity is per backend: the same nid from different backends is a
          // different item (distinct id namespaces), so dedup on backend + nid.
          $nid = $row['nid'] ?? NULL;
          $key = $id . ':' . $nid;
          if ($nid !== NULL && isset($seen[$key])) {
            continue;
          }
          if ($nid !== NULL) {
            $seen[$key] = TRUE;
          }
          // Stamp provenance so a saved item can be routed back to its backend.
          $row['backend'] = $id;
          $rows[] = $row;
        }
      }
      $base += $count;
    }
    return $rows;
  }

  /**
   * Merges facets across backends, summing option counts per filter.
   */
  protected function mergeFacets(array $backends, array $parameters): array {
    $merged = [];
    foreach ($backends as $backend) {
      foreach ($backend->getFacets($parameters) as $key => $options) {
        $merged[$key] = $this->mergeFacetOptions($merged[$key] ?? [], $options);
      }
    }
    return $merged;
  }

  /**
   * Merges a single facet's options, summing counts of equal filters.
   */
  protected function mergeFacetOptions(array $base, array $options): array {
    $index = [];
    foreach ($base as $i => $option) {
      $index[$this->facetOptionKey($option)] = $i;
    }
    foreach ($options as $option) {
      $key = $this->facetOptionKey($option);
      if (isset($index[$key])) {
        $base[$index[$key]]['count'] += $option['count'] ?? 0;
        continue;
      }
      $index[$key] = count($base);
      $base[] = $option;
    }
    return $base;
  }

  /**
   * Identity key for a facet option (static facets use value, others filter).
   */
  protected function facetOptionKey(array $option): string {
    return (string) ($option['filter'] ?? $option['value'] ?? '');
  }

  /**
   * Builds the location filter groups enriched with merged facet counts.
   */
  protected function groupedLocations(OpenyActivityFinderBackendInterface $primary, array $facets): array {
    $locations = $primary->getLocations();
    foreach ($locations as $key => $group) {
      $locations[$key]['count'] = 0;
      foreach ($group['value'] ?? [] as $location) {
        foreach ($facets['locations'] ?? [] as $facet) {
          if (isset($facet['id'], $location['value']) && $facet['id'] == $location['value']) {
            $locations[$key]['count'] += $facet['count'];
          }
        }
      }
    }
    return $locations;
  }

  /**
   * Builds the pager_info structure for a total result count.
   */
  protected function pagerInfo(int $count, int $per_page): array {
    $total_pages = (int) ceil($count / $per_page);
    $pages = [];
    for ($i = 1; $i <= $total_pages; $i++) {
      $pages[$i] = $i;
    }
    return ['total_pages' => $total_pages, 'pages' => $pages];
  }

}
