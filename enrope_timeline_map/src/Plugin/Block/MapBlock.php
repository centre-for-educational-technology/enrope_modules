<?php

namespace Drupal\enrope_timeline_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Enrope Map' Block.
 *
 * @Block(
 *   id = "enrope_map_block",
 *   admin_label = @Translation("Enrope map of partners block"),
 *   category = "Enrope",
 * )
 */
class MapBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'enrope_map_block_template',
      '#attached' => array(
        'library' => array(
          'enrope-timeline/jqvmap',
        ),
      ),
    );
  }

}