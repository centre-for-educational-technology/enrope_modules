<?php

namespace Drupal\enrope_group\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Change path '/user/login' to '/login'.
    if ($route = $collection->get('entity.group_content.group_node_relate_page')) {
      $route->setRequirement('_access', 'FALSE');
    }

    if($route = $collection->get('entity.group_content.group_node_add_page')){
      $route->setRequirement('_access', 'FALSE');
    }



  }

}