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

    //Disable Add node button on group nodes page
    if ($route = $collection->get('entity.group_content.group_node_relate_page')) {
      $route->setRequirement('_access', 'FALSE');
    }


    //Disable add new member button from Members listing
    if($route = $collection->get('entity.group_content.add_form')){
      $route->setRequirement('_access', 'FALSE');
    }



  }

}