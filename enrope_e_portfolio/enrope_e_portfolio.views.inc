<?php
/**
 * Implements hook_views_data_alter().
 */
function enrope_e_portfolio_views_data_alter(array &$data) {
  $data['node']['answer_task_status'] = array(
    'title' => t('Status of task answer'),
    'group' => t('Content'),
    'field' => array(
      'title' => t('Status of task answer'),
      'help' => t('Status of task answer'),
      'id' => 'answer_task_status',
    ),
  );
  $data['node']['answer_task_action'] = array(
    'title' => t('Action for task answer'),
    'group' => t('Content'),
    'field' => array(
      'title' => t('Action for task answer'),
      'help' => t('Action for task answer'),
      'id' => 'answer_task_action',
    ),
  );
}
