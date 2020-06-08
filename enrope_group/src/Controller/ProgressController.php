<?php


namespace Drupal\enrope_group\Controller;


use Drupal;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Link;
use Drupal\group\Entity\Group;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProgressController extends ControllerBase
{

  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('database')
    );
  }

  /**
   * TableSortExampleController constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database)
  {
    $this->database = $database;
  }

  public function getGroupUsers($group, $header)
  {

    //$db = Drupal::database();
    //$result = $db->query("SELECT entity_id FROM {group_content_field_data} WHERE gid = :gid AND type='isp_group_type-group_membership'", [':gid' => $group->id()])->fetchAll();


    $query = $this->database->select('group_content_field_data', 'g_data');
    $query->fields('g_data', array('entity_id'));
    $query->fields('first', array('field_first_name_value'));
    $query->fields('last', array('field_last_name_value'));
    $query->condition('g_data.gid', $group->id());
    $query->condition('g_data.type', 'isp_group_type-group_membership');
    $query->join('user__field_first_name', 'first', 'first.entity_id = g_data.entity_id');
    $query->join('user__field_last_name', 'last', 'last.entity_id = g_data.entity_id');

    $query = $query
      ->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);

    $result = $query->execute()->fetchAll();


    return $result;
  }


  public function getGroupTasks($group)
  {

    //$db = Drupal::database();
    $result = $this->database->query("SELECT entity_id FROM {group_content_field_data} WHERE gid = :gid AND type='group_content_type_bb5f5f86ef179'", [':gid' => $group->id()])->fetchAll();

    return $result;

  }

  /**
   * Helper function to get full user name with link or without
   * @param $user_id
   * @param bool $with_link
   * @return Link|string
   */
  public function getFullUserName($user_id, $with_link = false)
  {

    $user = User::load($user_id);
    $first_name = $user->get('field_first_name')->getValue()[0]['value'];
    $last_name = $user->get('field_last_name')->getValue()[0]['value'];

    if ($with_link) {
      return Link::createFromRoute($first_name . ' ' . $last_name, 'entity.user.canonical', ['user' => $user_id]);
    }
    return $first_name . ' ' . $last_name;

  }

  public function isTaskAnswerPublished($user_id, $task)
  {

    //$db = Drupal::database();
    $query = $this->database->select('node__field_connected_task', 'con_task');
    $query->fields('con_task', array('entity_id'));
    $query->condition('con_task.field_connected_task_target_id', $task->id());
    $query->join('node_field_data', 'n', 'con_task.entity_id = n.nid');
    $query->condition('n.uid', $user_id);

    $result = $query->execute()->fetchAll();

    return $result;

  }


  public function content(Group $group)
  {
    //Get titles ready
    $header = [
      ['data' => '#'],
      ['data' => t('Member'), 'field' => 'last.field_last_name_value', 'sort' => 'asc'],
    ];

    $users = $this->getGroupUsers($group, $header);
    $tasks = $this->getGroupTasks($group);


    if (!empty($tasks)) {
      $tasks = array_column($tasks, 'entity_id');

      $task_nodes = Node::loadMultiple($tasks);


      $task_num = 1;
      foreach ($task_nodes as $task_node) {

        $heading = new FormattableMarkup('<h3><a href="#" data-toggle="tooltip" title="@title">Task @num</a></h3>', ['@title' => $task_node->getTitle(), '@num' => $task_num]);
        $task_num++;


        array_push($header, array('data' => $heading));

      }

      if (!empty($users)) {

        $users_count = 1;

        foreach ($users as $user) {

          $row = [];
          $row[] = $users_count;
          $users_count++;

          $user_full_name_with_link = Link::createFromRoute($user->field_first_name_value . ' ' . $user->field_last_name_value, 'entity.user.canonical', ['user' => $user->entity_id]);
          array_push($row, $user_full_name_with_link);

          foreach ($task_nodes as $task_node) {

            $answered = $this->isTaskAnswerPublished($user->entity_id, $task_node);

            if (!empty($answered)) {


              $row[] = array('data' => Link::createFromRoute('Answered', 'entity.node.canonical', ['node' => $answered[0]->entity_id]), 'class' => 'bg-success');


            } else {
              $row[] = '';
            }

          }

          $rows[] = $row;

        }
      }


      $build['progress_table'] = array(
        '#theme' => 'table', '#header' => $header,
        '#rows' => $rows
      );

    } else {
      return [
        '#type' => 'markup',
        '#markup' => t('No tasks found'),
      ];
    }

    return $build;

  }


}
