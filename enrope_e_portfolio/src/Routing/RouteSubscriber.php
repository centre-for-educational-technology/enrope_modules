<?php

namespace Drupal\enrope_e_portfolio\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Session\AccountInterface;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {




  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('view.annotated_bibliography_by_user.page_1')) {
      $route->setRequirement('_custom_access', '\Drupal\enrope_e_portfolio\Routing\RouteSubscriber::tabOwnerAccessOverride');
    }
  }

  /**
   * Hide tab if that's not my own account
   */
  public static function tabOwnerAccessOverride(AccountInterface $account) {
    $user = \Drupal::routeMatch()->getParameter('user');

    $user_id = is_numeric($user)? $user : $user->id();

    if ($user_id == $account->id()) {

      return new AccessResultAllowed();
    }
    else {
      return new AccessResultForbidden();
    }
  }

}