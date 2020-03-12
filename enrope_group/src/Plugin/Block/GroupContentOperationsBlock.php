<?php

namespace Drupal\enrope_group\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with operations the user can perform on a group.
 *
 * @Block(
 *   id = "group_content_operations",
 *   admin_label = @Translation("Group content operations"),
 *   context = {
 *     "group" = @ContextDefinition("entity:group", required = FALSE)
 *   }
 * )
 */
class GroupContentOperationsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // This block varies per group type and per current user's group membership
    // permissions. Different group types could have different content plugins
    // enabled, influencing which group operations are available to them. The
    // active user's group permissions define which actions are accessible.
    //
    // We do not need to specify the current user or group as cache contexts
    // because, in essence, a group membership is a union of both.
    //$build['#cache']['contexts'] = ['group.type', 'group_membership.roles.permissions'];
    $build['#cache']['contexts'] = ['group.type'];

    // Of special note is the cache context 'group_membership.audience'. Where
    // the above cache contexts should suffice if everything is ran through the
    // permission system, group operations are an exception. Some operations
    // such as 'join' and 'leave' not only check for a permission, but also the
    // audience the user belongs to. I.e.: whether they're a 'member', an
    // 'outsider' or 'anonymous'.
    $build['#cache']['contexts'][] = 'group_membership.audience';

    /** @var \Drupal\group\Entity\GroupInterface $group */
    if (($group = $this->getContextValue('group')) && $group->id()) {

      $route_name = \Drupal::routeMatch()->getRouteName();

      $pluginId = $this->getPluginIdByRouteName($route_name);

      $link = [];

      // Retrieve the operations from the installed content plugins.
      foreach ($group->getGroupType()->getInstalledContentPlugins() as $plugin) {
        /** @var \Drupal\group\Plugin\GroupContentEnablerInterface $plugin */

        if($plugin->getPluginId() == $pluginId){

          $link = $plugin->getGroupOperations($group);
        }
      }



      if ($link) {

        // Create an operations element with all of the links.
        $build['#type'] = 'operations';
        $build['#prefix'] = '<button class="btn btn-default">';
        $build['#suffix'] = '</button>';
        $build['#links'] = $link;
      }
    }

    // If no group was found, cache the empty result on the route.
    return $build;
  }

  private function getPluginIdByRouteName($route_name){

    if(strstr($route_name, 'group_article')){
      return "group_node:group_article";
    }
    elseif (strstr($route_name, 'files')){
      return "group_node:file";
    }
    elseif (strstr($route_name, 'group_page')){
      return "group_node:group_page";
    }
    elseif (strstr($route_name, 'resources')){
      return "group_node:resources";
    }
    elseif (strstr($route_name, 'discussion_board')){
      return "group_node:discussion_board";
    }
    elseif (strstr($route_name, 'group_ta')){
      return "group_node:group_task";
    }

    else{
      return null;
    }

  }

  public function getCacheMaxAge() {
    return 0;
  }


}


