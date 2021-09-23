<?php

namespace Drupal\enrope_follow_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;

/**
 * Returns responses for Enrope Follow User routes.
 */
class EnropeFollowUserController extends ControllerBase
{

  /**
   * Builds the response.
   */
  public function follow()
  {

    $user = \Drupal::routeMatch()->getRawParameter('user');
    $user_to_follow_id = \Drupal::request()->query->get('user_to_follow');

    $user_to_follow = \Drupal::entityQuery('user')
        ->condition('uid', $user_to_follow_id)
        ->execute();


    $connection = \Drupal::service('database');

    if (!empty($user_to_follow)) {


      $connection->insert('enrope_follow_user_data')
          ->fields(['uid', 'created', 'following_uid'])
          ->values([
              'uid' => $user,
              'created' => \Drupal::time()->getRequestTime(),
              'following_uid' => $user_to_follow_id,
          ])
          ->execute();
    }


    return $this->build();

  }

  public function unfollow()
  {

    $user = \Drupal::routeMatch()->getRawParameter('user');
    $user_to_unfollow_id = \Drupal::request()->query->get('user_to_unfollow');

    $user_to_unfollow = \Drupal::entityQuery('user')
        ->condition('uid', $user_to_unfollow_id)
        ->execute();


    $connection = \Drupal::service('database');

    if (!empty($user_to_unfollow)) {

      $connection->delete('enrope_follow_user_data')
          ->condition('uid', $user)
          ->condition('following_uid', $user_to_unfollow_id)
          ->execute();

    }
    return $this->build();
  }


  public function build()
  {

    $user = \Drupal::routeMatch()->getRawParameter('user');
    $connection = \Drupal::service('database');

    $user_following = $connection->select('enrope_follow_user_data')
        ->fields('enrope_follow_user_data', ['following_uid'])
        ->condition('uid', $user)
        ->execute()
        ->fetchCol();


    if (!empty($user_following)) {
      $following_users = User::loadMultiple($user_following);

      $following_users_view = \Drupal::entityTypeManager()->getViewBuilder('user')->viewMultiple($following_users, 'enrope_user_in_members_list');
      $build = [
          '#type' => 'markup',
          '#markup' => render($following_users_view),
          '#prefix' => '<div class="alert alert-primary" role="alert">
                      ' . t("People I follow") . '
                    </div><div class="view-content row">',
          '#suffix' => '</div>',
      ];
    } else {
      $build = [
          '#type' => 'markup',
          '#markup' => '<div class="alert alert-primary" role="alert">
                      ' . t("No followings") . '
                    </div>',
      ];
    }


    return $build;
  }

}
