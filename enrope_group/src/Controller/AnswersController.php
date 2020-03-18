<?php


namespace Drupal\enrope_group\Controller;


use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Link;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AnswersController extends ControllerBase
{
  /**
   * The active database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('database'));
  }



  public function getOutput(NodeInterface $node)
  {

    //$answers = db_query("SELECT entity_id FROM {node__field_connected_task} WHERE field_connected_task_target_id = :task_id", [':task_id' => $node->id()])->fetchAll();





    // Prepare _sortable_ table header
    $header = array(
      array('data' => t('#')),
      array('data' => t('First name')),
      array('data' => t('Last name')),
      array('data' => t('Answer')),
    );

    $count = 1;

    $query = $this->database->select('node__field_connected_task','con_task');
    $query->fields('con_task', array('entity_id'));
    $query->condition('con_task.field_connected_task_target_id', $node->id());
    $result = $query->execute()->fetchAll();

    if(!empty($result)){
      $result = array_column($result, 'entity_id');

      $answers = node_load_multiple($result);
      foreach($answers as $answer) {

        $rows[] = [$count++, $answer->getRevisionAuthor()->get('field_first_name')->getValue()[0]['value'], $answer->getRevisionAuthor()->get('field_last_name')->getValue()[0]['value'], Link::createFromRoute($answer->getTitle(), 'entity.node.canonical', ['node' => $answer->id()])];
      }

      $build['answers_table'] = array(
        '#theme' => 'table', '#header' => $header,
        '#rows' => $rows
      );
    }else{
      return [
        '#type' => 'markup',
        '#markup' => t('No answers found'),
      ];
    }







    return $build;

  }

  public function getTitle()
  {

    return t('Answers');

  }

  public function isAllowedAnswersList($node){

    $node_obj = node_load($node);
    return AccessResult::allowedIf($node_obj->getType() == 'group_task');

  }


}
