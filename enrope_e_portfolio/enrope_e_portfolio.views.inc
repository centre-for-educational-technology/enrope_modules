<?php
/**
 * Implements hook_views_data_alter().
 */
function enrope_e_portfolio_views_data_alter(array &$data) {
  $data['node']['node_type_flagger'] = array(
    'title' => t('Node type flagger'),
    'group' => t('Content'),
    'field' => array(
      'title' => t('Node type flagger'),
      'help' => t('Flags a specific node type.'),
      'id' => 'node_type_flagger',
    ),
  );
}
