<?php
/**
 * @file
 * Contains \Drupal\enrope\Controller\EnropeTimelinePage.
 */
namespace Drupal\enrope_timeline_map\Controller;
use Drupal\Core\Controller\ControllerBase;

class EnropeLogoBlockController extends ControllerBase  {
  public function content() {
    return array(
      '#theme' => 'enrope_logo_block_template'
    );
  }
}