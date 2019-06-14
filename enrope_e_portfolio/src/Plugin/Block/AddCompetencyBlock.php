<?php

namespace Drupal\enrope_e_portfolio\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 *
 * @Block(
 *   id = "add_competency_block",
 *   admin_label = @Translation("Add competency block"),
 * )
 */
class AddCompetencyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {


    $build['link'] = [
        '#type' => 'link',
        '#title' => 'Add competency',
        '#url' => Url::fromRoute('node.add', ['node_type' => 'portfolio_competency']),
        '#attributes' => [
            'class' => ['btn btn-success mb-4'],
        ],
    ];



    // If no group was found, cache the empty result on the route.
    return $build;
  }



}


