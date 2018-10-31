<?php
/**
 * @file
 * Contains \Drupal\enrope\Controller\EnropeTimelinePage.
 */
namespace Drupal\enrope_timeline\Controller;
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