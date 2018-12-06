<?php

namespace Drupal\enrope_timeline_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Enrope Logo' Block.
 *
 * @Block(
 *   id = "enrope_logo_block",
 *   admin_label = @Translation("Enrope logo with desc block"),
 *   category = "Enrope",
 * )
 */
class LogoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'enrope_logo_block_template',
    );
  }

}