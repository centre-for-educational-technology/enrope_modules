<?php


namespace Drupal\enrope_bibliography\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

class ExportButtonBlock extends BlockBase
{

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * If a block should not be rendered because it has no content, then this
   * method must also ensure to return no content: it must then only return an
   * empty array, or an empty array with #cache set (with cacheability metadata
   * indicating the circumstances for it being empty).
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build()
  {
    $build['link'] = [
      '#type' => 'link',
      '#title' => 'Export',
      '#url' => Url::fromRoute('node.add', ['node_type' => 'annotated_bibliography']),
      '#attributes' => [
        'class' => ['btn btn-success mb-4'],
      ],
    ];
  }
}
