<?php
/**
 * @file
 * Contains \Drupal\enrope\Controller\EnropeTimelinePage.
 */
namespace Drupal\enrope_bibliography\Controller;
use Drupal\Core\Controller\ControllerBase;

class EnropeBibDisclaimerBlockController extends ControllerBase
{
  public function content() {
    return array(
      '#theme' => 'enrope_bibliography_disclaimer_block_template'
    );
  }

}
