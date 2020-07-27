<?php


namespace Drupal\enrope_e_portfolio\Plugin\Block;

use Drupal;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


/**
 *
 * @Block(
 *   id = "portfolio_items_block",
 *   admin_label = @Translation("Portfolio items block"),
 * )
 */
class PortfolioItemsBlock extends BlockBase
{


  public function build()
  {
    $node = \Drupal::routeMatch()->getParameter('node');
    $portfolio = Node::load($node->id());


    $showcases_rows_task_count = $this->getPortfolioItemsRowsWithDoneTasksCount('portfolio_showcase', $portfolio);
    $competency_rows_task_count = $this->getPortfolioItemsRowsWithDoneTasksCount('portfolio_competency', $portfolio);
    $autobiography_rows_task_count = $this->getPortfolioItemsRowsWithDoneTasksCount('autobiography', $portfolio);

    $showcases_rows = $showcases_rows_task_count['rows'];
    $competency_rows = $competency_rows_task_count['rows'];
    $autobiography_rows = $autobiography_rows_task_count['rows'];

    $showcases_tasks_done = $showcases_rows_task_count['done_tasks'];
    $competency_tasks_done = $competency_rows_task_count['done_tasks'];
    $autobiography_tasks_done = $autobiography_rows_task_count['done_tasks'];


    //Get titles ready
    $header = [
      ['data' => '#'],
      ['data' => t('Title')],
      ['data' => t('Task title')],
      ['data' => t('Type')],
      ['data' => t('Key notion')],
      ['data' => t('Date created')],
      ['data' => t('Date modified')],
    ];


    $group_showcases_tasks_count = 0;
    $group_competency_tasks_count = 0;
    $group_autobiography_tasks_count = 0;

    $user_groups = $this->getUserGroups($portfolio->getOwnerId());
    if (!empty($user_groups)) {
      foreach (array_column($user_groups, 'gid') as $group) {
        $group_showcases_tasks_count += $this->getGroupTasksCountByPortfolioType($group, 'showcase');
        $group_competency_tasks_count += $this->getGroupTasksCountByPortfolioType($group, 'competency');
        $group_autobiography_tasks_count += $this->getGroupTasksCountByPortfolioType($group, 'autobiography');
      }
    }


    if ($group_showcases_tasks_count > 0) {
      $done_showcases = $showcases_tasks_done * 100 / $group_showcases_tasks_count;
    } else {
      $done_showcases = 0;
    }

    if ($group_competency_tasks_count > 0) {
      $done_competency = $competency_tasks_done * 100 / $group_competency_tasks_count;
    } else {
      $done_competency = 0;
    }

    if ($group_autobiography_tasks_count > 0) {
      $done_autobiography = $autobiography_tasks_done * 100 / $group_autobiography_tasks_count;
    } else {
      $done_autobiography = 0;
    }


    $build['showcases']['showcases_header'] = array(
      '#markup' => '<h3>' . t('Showcases') . '</h3>',
      '#type' => 'markup',
    );

    $build['showcases']['showcases_progress'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="progress">
  <div class="progress-bar progress-bar-animated" role="progressbar" aria-valuenow="{{done_showcases}}" aria-valuemin="0" aria-valuemax="100" style="width: {{done_showcases}}%"></div>
</div>',
      '#context' => [
        'done_showcases' => $done_showcases,
      ]
    ];
    $build['showcases']['showcases_table'] = array(
      '#theme' => 'table', '#header' => $header,
      '#rows' => $showcases_rows
    );


    $build['competency']['competency_header'] = array(
      '#markup' => '<h3>' . t('Competency') . '</h3>',
      '#type' => 'markup',
    );

    $build['competency']['competency_progress'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="progress">
  <div class="progress-bar progress-bar-animated" role="progressbar" aria-valuenow="{{done_competency}}" aria-valuemin="0" aria-valuemax="100" style="width: {{done_competency}}%"></div>
</div>',
      '#context' => [
        'done_competency' => $done_competency,
      ]
    ];
    $build['competency']['competency_table'] = array(
      '#theme' => 'table', '#header' => $header,
      '#rows' => $competency_rows
    );


    $build['autobiography']['autobiography_header'] = array(
      '#markup' => '<h3>' . t('Autobiography') . '</h3>',
      '#type' => 'markup',
    );

    $build['autobiography']['autobiography_progress'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="progress">
  <div class="progress-bar progress-bar-animated" role="progressbar" aria-valuenow="{{done_autobiography}}" aria-valuemin="0" aria-valuemax="100" style="width: {{done_autobiography}}%"></div>
</div>',
      '#context' => [
        'done_autobiography' => $done_autobiography,
      ]
    ];
    $build['autobiography']['autobiography_table'] = array(
      '#theme' => 'table', '#header' => $header,
      '#rows' => $autobiography_rows
    );


    return $build;
  }

  public function getGroupTasksCountByPortfolioType($group, $sec_type)
  {

    $db = Drupal::database();
    $result = $db->query("SELECT COUNT(DISTINCT gd.entity_id) AS count FROM group_content_field_data AS gd LEFT JOIN node__field_portfolio_section AS ps ON ps.entity_id = gd.entity_id WHERE gd.gid = :gid AND gd.type='group_content_type_bb5f5f86ef179' AND ps.field_portfolio_section_value = :type;", [':gid' => $group, ':type' => $sec_type])->fetchAll();

    if (!empty($result)) {
      return $result[0]->count;
    }
    return 0;

  }

  public function getUserGroups($user)
  {
    $db = Drupal::database();

    $query = $db->select('group_content_field_data', 'g');
    $query->fields('g', array('gid'));
    $query->condition('g.type', 'isp_group_type-group_membership');
    $query->condition('g.entity_id', $user);
    $result = $query->execute()->fetchAll();

    return $result;

  }


  public function getPortfolioItemsRowsWithDoneTasksCount($type, $portfolio)
  {
    $database = \Drupal::database();

    $count = 1;
    $done_tasks = 0;

    $query_items = $database->select('node_field_data', 'n');
    $query_items->fields('n', array('nid'));
    $query_items->condition('n.type', $type);
    $query_items->condition('n.status', 1);
    $query_items->condition('n.uid', $portfolio->getOwnerId());
    $result_items = $query_items->execute()->fetchAll();

    $rows = [];

    if (!empty($result_items)) {

      foreach ($result_items as $item) {


        $item_node = Node::load($item->nid);


        $query_task = $database->select('node__field_connected_task', 'con_task');
        $query_task->fields('con_task', array('field_connected_task_target_id'));
        $query_task->condition('con_task.entity_id', $item_node->id());
        $result_task = $query_task->execute()->fetchAll();

        $date_created = $item_node->getCreatedTime();
        $date_created = \Drupal::service('date.formatter')->format($date_created, '$format');

        $date_modified = $item_node->getChangedTime();
        $date_modified = \Drupal::service('date.formatter')->format($date_modified, '$format');


        $task = !empty($result_task[0]) ? Node::load($result_task[0]->field_connected_task_target_id) : null;
        if (!empty($task)) {

          $task_keynotions = $task->get('field_key_notions')->getValue();

          if (!empty($task_keynotions)) {
            $task_keynotions = array_column($task_keynotions, 'target_id');


            $vocabulary_name = 'key_notions';
            $query = \Drupal::entityQuery('taxonomy_term');
            $query->condition('vid', $vocabulary_name);
            $query->condition('tid', $task_keynotions, 'IN');
            $tids = $query->execute();
            $terms = Term::loadMultiple($tids);


            $keynotions = array(
              'data' => array(
                '#markup' => '',
              ),
            );


            foreach ($terms as $term) {
              $name = $term->getName();
              $url = Link::createFromRoute($name, 'entity.taxonomy_term.canonical', ['taxonomy_term' => $term->id()]);
              $link = $url->toRenderable();
              $keynotions['data']['#markup'] .= render($link) . ' ';

            }


          }


          switch ($type) {
            case 'portfolio_showcase':
              $allowed_values = $item_node->getFieldDefinition('field_showcase_type')->getFieldStorageDefinition()->getSetting('allowed_values');
              $state_value = $item_node->get('field_showcase_type')->value;
              $item_type = !empty($state_value) ? $allowed_values[$state_value] : '';

              $done_tasks++;
              $row = [$count++, Link::createFromRoute($item_node->getTitle(), 'entity.node.canonical', ['node' => $item_node->id()]), Link::createFromRoute($task->getTitle(), 'entity.node.canonical', ['node' => $task->id()]), $item_type, !empty($keynotions) ? $keynotions : '', $date_created, $date_modified];

              break;
            case 'portfolio_competency':
              $allowed_values = $item_node->getFieldDefinition('field_competency_type')->getFieldStorageDefinition()->getSetting('allowed_values');
              $state_value = $item_node->get('field_competency_type')->value;
              $item_type = !empty($state_value) ? $allowed_values[$state_value] : '';

              $done_tasks++;

              $row = [$count++, Link::createFromRoute($item_node->getTitle(), 'entity.node.canonical', ['node' => $item_node->id()]), Link::createFromRoute($task->getTitle(), 'entity.node.canonical', ['node' => $task->id()]), $item_type, !empty($keynotions) ? $keynotions : '', $date_created, $date_modified];

              break;
            case 'autobiography':
              $allowed_values = $item_node->getFieldDefinition('field_autobiography_type')->getFieldStorageDefinition()->getSetting('allowed_values');
              $state_value = $item_node->get('field_autobiography_type')->value;
              $item_type = !empty($state_value) ? $allowed_values[$state_value] : '';

              $done_tasks++;
              $row = [$count++, Link::createFromRoute($item_node->getTitle(), 'entity.node.canonical', ['node' => $item_node->id()]), Link::createFromRoute($task->getTitle(), 'entity.node.canonical', ['node' => $task->id()]), $item_type, !empty($keynotions) ? $keynotions : '', $date_created, $date_modified];

              break;
          }
        } else {
          switch ($type) {
            case 'portfolio_showcase':
              $allowed_values = $item_node->getFieldDefinition('field_showcase_type')->getFieldStorageDefinition()->getSetting('allowed_values');
              $state_value = $item_node->get('field_showcase_type')->value;
              $item_type = !empty($state_value) ? $allowed_values[$state_value] : '';

              $row = [$count++, Link::createFromRoute($item_node->getTitle(), 'entity.node.canonical', ['node' => $item_node->id()]), '', $item_type, '', $date_created, $date_modified];

              break;
            case 'portfolio_competency':
              $allowed_values = $item_node->getFieldDefinition('field_competency_type')->getFieldStorageDefinition()->getSetting('allowed_values');
              $state_value = $item_node->get('field_competency_type')->value;
              $item_type = !empty($state_value) ? $allowed_values[$state_value] : '';

              $row = [$count++, Link::createFromRoute($item_node->getTitle(), 'entity.node.canonical', ['node' => $item_node->id()]), '', $item_type, '', $date_created, $date_modified];

              break;
            case 'autobiography':
              $allowed_values = $item_node->getFieldDefinition('field_autobiography_type')->getFieldStorageDefinition()->getSetting('allowed_values');
              $state_value = $item_node->get('field_autobiography_type')->value;
              $item_type = !empty($state_value) ? $allowed_values[$state_value] : '';

              $row = [$count++, Link::createFromRoute($item_node->getTitle(), 'entity.node.canonical', ['node' => $item_node->id()]), '', $item_type, '', $date_created, $date_modified];

              break;
          }
        }


        $rows[] = $row;
      }

    }

    return array('rows' => $rows, 'done_tasks' => $done_tasks);
  }

  public function getCacheMaxAge()
  {
    return 0;
  }
}
