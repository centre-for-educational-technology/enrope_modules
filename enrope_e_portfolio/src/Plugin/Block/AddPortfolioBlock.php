<?php

namespace Drupal\enrope_e_portfolio\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 *
 * @Block(
 *   id = "add_e_portfolio_block",
 *   admin_label = @Translation("Add e-Portfolio block"),
 * )
 */
class AddPortfolioBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {


    $build['link'] = [
        '#type' => 'link',
        '#title' => 'Add e-Portfolio',
        '#url' => Url::fromRoute('node.add', ['node_type' => 'e_portfolio']),
        '#attributes' => [
            'class' => ['btn btn-success mb-4'],
        ],
    ];



    // If no group was found, cache the empty result on the route.
    return $build;
  }



}


