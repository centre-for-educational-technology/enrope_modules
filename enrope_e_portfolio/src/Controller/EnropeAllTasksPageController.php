<?php


namespace Drupal\enrope_e_portfolio\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\group\Entity\Group;
use Drupal\node\Entity\Node;

class EnropeAllTasksPageController extends ControllerBase
{
  public function content()
  {

    $header = [
      'title' => [
        'data' => $this->t('Title'),
        'specifier' => 'title',
      ],
      'group' => [
        'data' => $this->t('Group'),
        'specifier' => 'group',
      ],
    ];


    $database = \Drupal::database();

    $query = $database->select('node_field_data', 'n');
    $query->condition('n.type', 'group_task')
      ->condition('status', 1)
      ->orderBy('n.created', 'DESC');

    $query->innerJoin('group_content_field_data', 'g', 'g.entity_id = n.nid');
    $query->innerJoin('groups_field_data', 'gt', 'gt.id = g.gid');
    $query
      ->fields('g', array('gid'))
      ->fields('gt', array('label'))
      ->fields('n', array('nid', 'created'));
    $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(10);
    $result = $query->distinct()->execute()->fetchAll();


    $rows = [];
    foreach ($result as $node) {
      $url_group = \Drupal\Core\Url::fromRoute('entity.group.canonical', ['group' => $node->gid]);
      $link_group = Link::fromTextAndUrl($node->label, $url_group);
      $row = [];
      $row[] = Node::load($node->nid)->toLink()->toString();;

      $row[] = $link_group;
      $rows[] = $row;
    }

    $build['table'] = [
      '#type' => 'table',
      '#prefix' => '<div class="alert alert-warning" role="alert">
 These are all tasks available for ENROPE members. You are able to view and submit your answer only if you are a member of the group where task belongs to
</div>',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No content has been found.'),
    ];

    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;


  }


}
