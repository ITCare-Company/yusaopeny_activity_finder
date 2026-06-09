<?php

namespace Drupal\openy_activity_finder\Plugin\ActivityFinderBackend;

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\openy_activity_finder\Attribute\ActivityFinderBackend;
use Drupal\openy_activity_finder\Plugin\ActivityFinderBackendPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Mock backend plugin.
 *
 * Serves Activity Finder from static fixtures captured from a real Solr
 * response, with zero Solr/DB (W0b DECISIONS D4). Lets AF4 run and the Vue 3
 * migration proceed without standing up Solr. Fixtures validate against
 * fixtures/schema/runProgramSearch.schema.json (D10).
 */
#[ActivityFinderBackend(
  id: 'mock',
  label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Mock (fixtures, no Solr)'),
)]
class MockBackend extends ActivityFinderBackendPluginBase {

  use StringTranslationTrait;

  /**
   * Absolute path to the fixtures directory.
   *
   * @var string
   */
  protected string $fixturesDir;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    /** @var \Drupal\Core\Extension\ModuleExtensionList $module_list */
    $module_list = $container->get('extension.list.module');
    $instance->fixturesDir = $module_list->getPath('openy_activity_finder') . '/fixtures';
    return $instance;
  }

  /**
   * Loads and decodes a fixture file.
   *
   * @param string $name
   *   Fixture file name without extension.
   * @param mixed $default
   *   Value returned when the fixture is missing.
   *
   * @return mixed
   *   The decoded fixture, or $default.
   */
  protected function fixture(string $name, $default = []) {
    $file = $this->fixturesDir . '/' . $name . '.json';
    if (!is_readable($file)) {
      return $default;
    }
    $data = json_decode(file_get_contents($file), TRUE);
    return $data ?? $default;
  }

  /**
   * Loads the captured full search response fixture, shifted to the present.
   */
  protected function searchFixture(): array {
    $data = $this->fixture('runProgramSearch_empty', [
      'count' => 0,
      'facets' => [],
      'table' => [],
      'groupedLocations' => [],
    ]);
    return $this->shiftToNow($data);
  }

  /**
   * Whole-month offset from the fixture's capture date to today.
   *
   * Keeps Mock results in the current timeframe so captured dates never drift
   * into the past as real time advances.
   */
  protected function shiftMonths(int &$captured_year): int {
    $meta = $this->fixture('_meta', ['captured_year' => 2026, 'captured_month' => 6]);
    $captured_year = (int) $meta['captured_year'];
    $now = new \DateTime('now');
    return ((int) $now->format('Y') * 12 + (int) $now->format('n'))
      - ($captured_year * 12 + (int) $meta['captured_month']);
  }

  /**
   * Shifts the date-bearing fields of a search response forward to the present.
   */
  protected function shiftToNow(array $data): array {
    $captured_year = 2026;
    $delta = $this->shiftMonths($captured_year);
    if ($delta === 0) {
      return $data;
    }
    foreach (($data['table'] ?? []) as $i => $row) {
      if (!empty($row['dates'])) {
        $data['table'][$i]['dates'] = $this->shiftDateString((string) $row['dates'], $captured_year, $delta);
      }
    }
    foreach (($data['facets']['af_start_month'] ?? []) as $i => $facet) {
      if (isset($facet['filter']) && is_numeric($facet['filter'])) {
        $data['facets']['af_start_month'][$i]['filter'] = (string) ((((int) $facet['filter'] - 1 + $delta) % 12 + 12) % 12 + 1);
      }
    }
    return $data;
  }

  /**
   * Shifts a rendered "M d" date by whole months, keeping the "M d" format.
   */
  protected function shiftDateString(string $value, int $captured_year, int $delta): string {
    $date = \DateTime::createFromFormat('M d Y', "$value $captured_year");
    if (!$date) {
      return $value;
    }
    $date->modify(($delta >= 0 ? '+' : '-') . abs($delta) . ' months');
    return $date->format('M d');
  }

  /**
   * Filters the fixture rows by the search parameters.
   *
   * Best-effort, in-memory matching over the fields the captured rows carry
   * (location, activity category, keyword, day). Not full Solr parity — enough
   * to exercise the wizard without Solr.
   *
   * @param array $parameters
   *   Search parameters.
   *
   * @return array
   *   Matching rows.
   */
  protected function filterRows(array $parameters): array {
    $rows = $this->searchFixture()['table'] ?? [];
    $ids = $parameters['ids'] ?? '';
    $rows = $this->filterByField($rows, 'nid', is_array($ids) ? implode(',', $ids) : $ids);
    $rows = $this->filterByField($rows, 'location_id', $parameters['locations'] ?? '');
    $rows = $this->filterByField($rows, 'program_id', $parameters['categories'] ?? '');
    $rows = $this->filterByKeyword($rows, $parameters['keywords'] ?? '');
    $rows = $this->filterByDays($rows, $parameters['days'] ?? '');
    // Block-level restrictions: limit (only these) and exclude (remove these).
    $rows = $this->filterByField($rows, 'program_id', $parameters['limit'] ?? '');
    $rows = $this->filterByField($rows, 'location_id', $parameters['limitloc'] ?? '');
    $rows = $this->excludeByField($rows, 'program_id', $parameters['exclude'] ?? '');
    $rows = $this->excludeByField($rows, 'location_id', $parameters['excludeloc'] ?? '');
    return array_values($rows);
  }

  /**
   * Keeps rows whose field value is in the comma-separated selection.
   */
  protected function filterByField(array $rows, string $field, string $csv): array {
    $selected = array_filter(explode(',', $csv));
    if (!$selected) {
      return $rows;
    }
    return array_filter($rows, fn($row) => in_array((string) ($row[$field] ?? ''), $selected, TRUE));
  }

  /**
   * Removes rows whose field value is in the comma-separated selection.
   */
  protected function excludeByField(array $rows, string $field, string $csv): array {
    $excluded = array_filter(explode(',', $csv));
    if (!$excluded) {
      return $rows;
    }
    return array_filter($rows, fn($row) => !in_array((string) ($row[$field] ?? ''), $excluded, TRUE));
  }

  /**
   * Keeps rows whose name or description contains the keyword.
   */
  protected function filterByKeyword(array $rows, string $keyword): array {
    $keyword = trim($keyword);
    if ($keyword === '') {
      return $rows;
    }
    return array_filter($rows, fn($row) => stripos(($row['name'] ?? '') . ' ' . ($row['description'] ?? ''), $keyword) !== FALSE);
  }

  /**
   * Keeps rows scheduled on any of the comma-separated day names.
   */
  protected function filterByDays(array $rows, string $csv): array {
    $selected = array_filter(array_map('strtolower', explode(',', $csv)));
    if (!$selected) {
      return $rows;
    }
    return array_filter($rows, function ($row) use ($selected) {
      $row_days = strtolower($row['days'] ?? '');
      foreach ($selected as $day) {
        if ($day !== '' && str_contains($row_days, $day)) {
          return TRUE;
        }
      }
      return FALSE;
    });
  }

  /**
   * {@inheritdoc}
   */
  public function getResultsCount(array $parameters): int {
    return count($this->filterRows($parameters));
  }

  /**
   * {@inheritdoc}
   */
  public function getFacets(array $parameters): array {
    return $this->searchFixture()['facets'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getResults(array $parameters, int $offset, int $limit, int $log_id): array {
    return array_slice($this->filterRows($parameters), $offset, $limit);
  }

  /**
   * {@inheritdoc}
   */
  public function getLocations() {
    return $this->fixture('getLocations');
  }

  /**
   * {@inheritdoc}
   */
  public function getSortOptions() {
    return $this->fixture('getSortOptions');
  }

  /**
   * {@inheritdoc}
   */
  public function getRelevanceSort() {
    return $this->fixture('getRelevanceSort', '');
  }

  /**
   * {@inheritdoc}
   */
  public function getAges() {
    return $this->fixture('getAges');
  }

  /**
   * {@inheritdoc}
   */
  public function getDaysOfWeek() {
    return $this->fixture('getDaysOfWeek');
  }

  /**
   * {@inheritdoc}
   */
  public function getPartsOfDay() {
    return $this->fixture('getPartsOfDay');
  }

  /**
   * {@inheritdoc}
   */
  public function getDaysTimes() {
    return $this->fixture('getDaysTimes');
  }

  /**
   * {@inheritdoc}
   */
  public function getStartMonths() {
    return $this->fixture('getStartMonths');
  }

  /**
   * {@inheritdoc}
   */
  public function getDurations() {
    return $this->fixture('getDurations');
  }

  /**
   * {@inheritdoc}
   */
  public function getWeeks() {
    return $this->fixture('getWeeks');
  }

  /**
   * {@inheritdoc}
   */
  public function getCategories() {
    return $this->fixture('getCategories');
  }

  /**
   * {@inheritdoc}
   */
  public function getCategoriesTopLevel() {
    return $this->fixture('getCategoriesTopLevel');
  }

  /**
   * {@inheritdoc}
   */
  public function getCategoriesType() {
    return $this->fixture('getCategoriesType', 'multiple');
  }

  /**
   * {@inheritdoc}
   */
  public function getFiltersSectionConfig() {
    return $this->fixture('getFiltersSectionConfig', []);
  }

  /**
   * {@inheritdoc}
   */
  public function getProgramsMoreInfo($request) {
    return [];
  }

}
