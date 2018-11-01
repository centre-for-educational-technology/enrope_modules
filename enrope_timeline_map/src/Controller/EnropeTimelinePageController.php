<?php
/**
 * @file
 * Contains \Drupal\enrope_timeline_map\Controller\EnropeTimelinePageController.
 */
namespace Drupal\enrope_timeline_map\Controller;
use Drupal\Core\Controller\ControllerBase;

class EnropeTimelinePageController extends ControllerBase  {
  public function content() {
    return array(
      '#theme' => 'enrope_timeline_template'
    );

    //    return array(
    //      '#theme' => 'enrope_timeline_template',
    //      '#test_var' => $this->t('Test Value'),
    //    );
  }
}