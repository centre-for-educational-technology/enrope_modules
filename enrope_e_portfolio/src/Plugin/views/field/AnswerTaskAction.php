<?php

/**
 * @file
 * Definition of Drupal\enrope_e_portfolio\Plugin\views\field\AnswerTaskAction.
 */

namespace Drupal\enrope_e_portfolio\Plugin\views\field;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to show action button according to answer status
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("answer_task_action")
 */
class AnswerTaskAction extends FieldPluginBase
{

  /**
   * @{inheritdoc}
   */
  public function query()
  {
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
      ->fields('ct', array('field_connected_task_target_id', 'entity_id'))
      ->fields('n', array('type'));
    $result = $query->execute()->fetchAll();

    if (!empty($result)) {
      $this->user_answers = $result;
    }

  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions()
  {
    $options = parent::defineOptions();
    $options['node_type'] = array('default' => 'group_task');

    return $options;
  }


  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values)
  {
    $node = $this->getEntity($values);
    if ($node->bundle() == $this->options['node_type']) {


      if (isset($this->user_answers)) {
        if (in_array($node->id(), array_column($this->user_answers, 'field_connected_task_target_id'))) {

          foreach ($this->user_answers as $answer){
            if($answer->field_connected_task_target_id == $node->id()){
              $answer_node_id = $answer->entity_id;
            }
          }


          $url = Url::fromRoute('entity.node.edit_form', array('node' => $answer_node_id));
          $link = Link::fromTextAndUrl(t('View/Edit answer'), $url);
          $link = $link->toRenderable();
          $link['#attributes'] = array('class' => array('btn', 'btn-primary'));
          $result = render($link);
          return $result;
        }
        return $this->add_answer_button($node);
      } else {
        return $this->add_answer_button($node);
      }

    }
    return '';
  }


  private function add_answer_button($node)
  {
    $type = $node->get('field_portfolio_section')->getValue();
    $type = array_column($type, 'value')[0];

    switch ($type){
      case 'autobiography':
        $type = 'autobiography';
        break;
      case 'showcase':
        $type = 'portfolio_showcase';
        break;
      case 'competences':
        $type = 'portfolio_competency';
        break;
    }


    $url = Url::fromRoute('node.add', ['node_type' => $type]);
    $link = Link::fromTextAndUrl(t('Submit answer'), $url);
    $link = $link->toRenderable();
    $link['#attributes'] = array('class' => array('btn', 'btn-primary'));

    $result = render($link);

    return $result;
  }
}
