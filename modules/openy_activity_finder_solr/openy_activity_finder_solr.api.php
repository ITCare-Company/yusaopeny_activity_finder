<?php

/**
 * @file
 * Hooks specific to the openy_activity_finder_solr module.
 */

use Drupal\node\NodeInterface;

/**
 * Alter a single processed Solr result row.
 *
 * Invoked by the Solr backend for each row while building the result table.
 *
 * @param array $data
 *   The processed result item for the program search.
 * @param \Drupal\node\NodeInterface $entity
 *   The node that has just been processed.
 *
 * @see \Drupal\openy_activity_finder_solr\OpenyActivityFinderSolrBackend::processResults()
 */
function hook_activity_finder_program_process_results_alter(array &$data, NodeInterface $entity) {
  $data['description'] = t('Test session description');
}
