<?php


namespace Drupal\enrope_news\Plugin\Block;


use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Enrope twitter Map' Block.
 *
 * @Block(
 *   id = "enrope_twitter_block",
 *   admin_label = @Translation("Enrope twitter block"),
 *   category = "Enrope",
 * )
 */
class NewsTwitterBlock extends BlockBase
{

  public function build()
  {
    return array(
        '#theme' => 'enrope_twitter_block_template',
    );

  }

}