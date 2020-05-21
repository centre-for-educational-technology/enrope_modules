<?php


namespace Drupal\enrope_e_portfolio\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\enrope_e_portfolio\Plugin\views\field\AnswerTaskAction;

/**
 *
 * @Block(
 *   id = "add_answer_block",
 *   admin_label = @Translation("Add answer block"),
 * )
 */
class AddAnswerBlock extends BlockBase
{

  public function build()
  {

    $node = \Drupal::routeMatch()->getParameter('node');

    $user_answers = $this->getUserAnswers();


    if (!empty($user_answers)) {
      if (in_array($node->id(), array_column($user_answers, 'field_connected_task_target_id'))) {

        foreach ($user_answers as $answer) {
          if ($answer->field_connected_task_target_id == $node->id()) {
            $answer_node_id = $answer->entity_id;
          }
        }


        $url = Url::fromRoute('entity.node.edit_form', array('node' => $answer_node_id));
        $link = Link::fromTextAndUrl(t('View/Edit answer'), $url);
        $link = $link->toRenderable();
        $link['#attributes'] = array('class' => array('btn', 'btn-primary'));

        return $link;
      }
    }


    return $this->add_answer_button($node);
  }

  /**
   * Similar to AnswerTaskAction->add_answer_button()
   * @param $node
   * @return array|mixed[]
   */
  private function add_answer_button($node)
  {
    $type = $node->get('field_portfolio_section')->getValue();
    $type = array_column($type, 'value')[0];

    switch ($type) {
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


    $url = Url::fromRoute('node.add', ['node_type' => $type, 'task_id' => $node->id()]);
    $link = Link::fromTextAndUrl(t('Submit answer'), $url);
    $link = $link->toRenderable();
    $link['#attributes'] = array('class' => array('btn', 'btn-primary'));

    return $link;
  }

  /*
   * Similar to AnswerTaskAction->query()
   */
  private function getUserAnswers()
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

    return $result;
  }

  public function getCacheMaxAge()
  {
    return 0;
  }
}
