<?php

namespace Drupal\enrope_group\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\group\Entity\Group;

/**
 * Provides a 'EnropeSupervisorsBlock' block.
 *
 * @Block(
 *  id = "enrope_supervisors_block",
 *  admin_label = @Translation("Enrope supervisors block"),
 * )
 */
class EnropeSupervisorsBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {

    $node = \Drupal::routeMatch()->getParameter('group');
    $group = Group::load($node->id());

    $supervisors = $group->getMembers('isp_group_type-supervisor');
    $users = array();
    foreach ($supervisors as $member) {
      array_push($users, $member->getUser());
    }

    $build = [];
    $build['#theme'] = 'enrope_supervisors_block';
    $build['#users'] = $users;

    return $build;
  }

  public function getCacheMaxAge()
  {
    return 0;
  }

}
