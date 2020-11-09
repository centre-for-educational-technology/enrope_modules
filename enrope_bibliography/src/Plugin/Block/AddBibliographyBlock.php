<?php

namespace Drupal\enrope_bibliography\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a block with operations the user can perform on a group.
 *
 * @Block(
 *   id = "add_annotated_bibliography_block",
 *   admin_label = @Translation("Add annotated bibliography block"),
 * )
 */
class AddBibliographyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {


    $build['link'] = [
        '#type' => 'link',
        '#title' => 'Add item to annotated bibliography',
        '#url' => Url::fromRoute('node.add', ['node_type' => 'annotated_bibliography']),
        '#attributes' => [
            'class' => ['btn btn-success mb-4'],
        ],
    ];



    // If no group was found, cache the empty result on the route.
    return $build;
  }



}


