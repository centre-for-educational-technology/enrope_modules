<?php
/**
 * @file
 * Contains \Drupal\enrope\Controller\EnropeTimelinePage.
 */
namespace Drupal\enrope_timeline_map\Controller;
use Drupal\Core\Controller\ControllerBase;

class EnropeMapBlockController extends ControllerBase  {
  public function content() {
    return array(
      '#theme' => 'enrope_map_block_template'
    );

    //    return array(
    //      '#theme' => 'enrope_timeline_template',
    //      '#test_var' => $this->t('Test Value'),
    //    );
  }
}