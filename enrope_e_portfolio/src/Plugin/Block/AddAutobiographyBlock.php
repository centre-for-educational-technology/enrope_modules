<?php

namespace Drupal\enrope_e_portfolio\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 *
 * @Block(
 *   id = "add_autobiography_block",
 *   admin_label = @Translation("Add autobiography block"),
 * )
 */
class AddAutobiographyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {


    $build['link'] = [
        '#type' => 'link',
        '#title' => 'Add autobiography',
        '#url' => Url::fromRoute('node.add', ['node_type' => 'autobiography']),
        '#attributes' => [
            'class' => ['btn btn-success mb-4'],
        ],
    ];



    // If no group was found, cache the empty result on the route.
    return $build;
  }



}


