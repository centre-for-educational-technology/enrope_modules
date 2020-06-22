<?php


namespace Drupal\enrope_e_portfolio\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 *   id = "published_showcase_block",
 *   admin_label = @Translation("Published showcase block"),
 * )
 */
class PublishedShowcasesBlock extends BlockBase
{

  public function build()
  {
    // TODO: Write logic

    $node = \Drupal::routeMatch()->getParameter('node');

    //Get titles ready
    $header = [
      ['data' => '#'],
      ['data' => t('Title')],
    ];

    $build['shocases_table'] = array(
      '#theme' => 'table', '#header' => $header,
    );

    return $build;
  }
}
