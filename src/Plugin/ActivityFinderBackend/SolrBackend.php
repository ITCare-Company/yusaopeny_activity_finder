<?php

namespace Drupal\openy_activity_finder\Plugin\ActivityFinderBackend;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\openy_activity_finder\Attribute\ActivityFinderBackend;
use Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface;
use Drupal\openy_activity_finder\Plugin\ActivityFinderBackendPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Solr backend plugin.
 *
 * Thin wrapper over the existing openy_activity_finder.solr_backend service.
 * Delegates every call unchanged so existing sites stay byte-identical
 * (W0b DECISIONS D3, D5). This is the default backend.
 */
#[ActivityFinderBackend(
  id: 'solr',
  label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Solr'),
)]
class SolrBackend extends ActivityFinderBackendPluginBase {

  use StringTranslationTrait;

  /**
   * The wrapped Solr backend service.
   *
   * @var \Drupal\openy_activity_finder\OpenyActivityFinderSolrBackend
   */
  protected $backend;

  /**
   * Memoized Search API result sets keyed by parameter hash.
   *
   * @var array
   */
  protected array $resultSets = [];

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->backend = $container->get('openy_activity_finder.solr_backend');
    return $instance;
  }

  /**
   * Runs (and memoizes) the Search API query for the given parameters.
   *
   * @param array $parameters
   *   Search parameters. Set 'af_count_only' for a rows-free count/facets
   *   query; otherwise the query is paginated by 'page'.
   *
   * @return \Drupal\search_api\Query\ResultSet
   *   The result set.
   */
  protected function resultSet(array $parameters) {
    $key = md5(json_encode($parameters));
    if (!isset($this->resultSets[$key])) {
      $this->resultSets[$key] = $this->backend->doSearchRequest($parameters);
    }
    return $this->resultSets[$key];
  }

  /**
   * {@inheritdoc}
   */
  public function getResultsCount(array $parameters): int {
    $parameters['af_count_only'] = TRUE;
    return (int) $this->resultSet($parameters)->getResultCount();
  }

  /**
   * {@inheritdoc}
   */
  public function getFacets(array $parameters): array {
    $parameters['af_count_only'] = TRUE;
    return $this->backend->getFacets($this->resultSet($parameters));
  }

  /**
   * {@inheritdoc}
   */
  public function getResults(array $parameters, int $offset, int $limit, int $log_id): array {
    $parameters['af_offset'] = $offset;
    $parameters['af_limit'] = $limit;
    return $this->backend->processResults($this->resultSet($parameters), $log_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getLocations() {
    return $this->backend->getLocations();
  }

  /**
   * {@inheritdoc}
   */
  public function getSortOptions() {
    return $this->backend->getSortOptions();
  }

  /**
   * {@inheritdoc}
   */
  public function getRelevanceSort() {
    return $this->backend->getRelevanceSort();
  }

  /**
   * {@inheritdoc}
   */
  public function getAges() {
    return $this->backend->getAges();
  }

  /**
   * {@inheritdoc}
   */
  public function getDaysOfWeek() {
    return $this->backend->getDaysOfWeek();
  }

  /**
   * {@inheritdoc}
   */
  public function getPartsOfDay() {
    return $this->backend->getPartsOfDay();
  }

  /**
   * {@inheritdoc}
   */
  public function getDaysTimes() {
    return $this->backend->getDaysTimes();
  }

  /**
   * {@inheritdoc}
   */
  public function getStartMonths() {
    return $this->backend->getStartMonths();
  }

  /**
   * {@inheritdoc}
   */
  public function getDurations() {
    return $this->backend->getDurations();
  }

  /**
   * {@inheritdoc}
   */
  public function getWeeks() {
    return $this->backend->getWeeks();
  }

  /**
   * {@inheritdoc}
   */
  public function getCategories() {
    return $this->backend->getCategories();
  }

  /**
   * {@inheritdoc}
   */
  public function getCategoriesType() {
    return $this->backend->getCategoriesType();
  }

  /**
   * {@inheritdoc}
   */
  public function getFiltersSectionConfig() {
    return $this->backend->getFiltersSectionConfig();
  }

  /**
   * {@inheritdoc}
   */
  public function getProgramsMoreInfo($request) {
    return $this->backend->getProgramsMoreInfo($request);
  }

}
