<?php

namespace Drupal\enrope_e_portfolio\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 *
 * @Block(
 *   id = "add_portfolio_item_block",
 *   admin_label = @Translation("Add portfolio item block"),
 * )
 */
class AddPortfolioItemBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {


    $build['link'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="dropdown" style="margin-bottom: 20px">
        <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Add portfolio item
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          {{autobiography}}
          {{competency}}
          {{showcase}}
        </div>
        </div>',
      '#context' => [
        'autobiography' => Link::createFromRoute(t('Autobiography'), 'node.add', ['node_type' => 'autobiography'], ['attributes' => ['class' => 'dropdown-item']])->toString(),
        'competency' => Link::createFromRoute(t('Competency'), 'node.add', ['node_type' => 'portfolio_competency'], ['attributes' => ['class' => 'dropdown-item']])->toString(),
        'showcase' => Link::createFromRoute(t('Showcase'), 'node.add', ['node_type' => 'portfolio_showcase'], ['attributes' => ['class' => 'dropdown-item']])->toString(),
      ]

    ];


    // If no group was found, cache the empty result on the route.
    return $build;
  }

  public function getCacheMaxAge()
  {
    return 0;
  }

}


