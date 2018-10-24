<?php
/**
 * @file
 * Contains \Drupal\enrope\Controller\EnropeTimelinePage.
 */
namespace Drupal\enrope_timeline\Controller;
class EnropeTimelinePageController {
  public function content() {
    return array(
      '#type' => 'markup',
      '#markup' => t('Hello, World!'),
    );
  }
}