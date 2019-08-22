<?php

namespace Drupal\enrope_bibliography\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a disclaimer block
 *
 * @Block(
 *   id = "annotated_bibliography_disclaimer_block",
 *   admin_label = @Translation("Annotated bibliography disclaimer block"),
 * )
 */
class BibliographyDisclaimerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'enrope_bibliography_disclaimer_block_template',
    );
  }



}


