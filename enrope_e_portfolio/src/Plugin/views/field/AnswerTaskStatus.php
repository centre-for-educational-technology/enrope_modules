<?php

/**
 * @file
 * Definition of Drupal\enrope_e_portfolio\Plugin\views\field\AnswerTaskStatus.
 */

namespace Drupal\enrope_e_portfolio\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field to show status of a task answer
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("answer_task_status")
 */
class AnswerTaskStatus extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    $uid = \Drupal::currentUser()->id();

    $database = \Drupal::database();
    $query = $database->select('node_field_data', 'n');
    $query->condition('n.uid', $uid);

    // Create the orConditionGroup
    $orGroup = $query->orConditionGroup()
      ->condition('n.type', 'portfolio_competency')
      ->condition('n.type', 'portfolio_showcase')
      ->condition('n.type', 'autobiography');

    // Add the group to the query.
    $query->condition($orGroup);

    $query->join('node__field_connected_task', 'ct', 'ct.entity_id = n.nid');
    $query
      ->fields('ct', array('field_connected_task_target_id'));
    $result = $query->execute()->fetchCol();

    if(!empty($result)){
      $this->user_answers = $result;
    }

  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['node_type'] = array('default' => 'group_task');

    return $options;
  }


  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $this->getEntity($values);
    if ($node->bundle() == $this->options['node_type']) {


      if(isset($this->user_answers)){
        if(in_array($node->id(), $this->user_answers)){
          return $this->t('Answered');
        }
        return $this->t('Not answered');
      }else{
        return $this->t('Not answered');
      }

    }
    return '';
  }
}
