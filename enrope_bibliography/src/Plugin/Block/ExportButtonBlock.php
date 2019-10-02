<?php


namespace Drupal\enrope_bibliography\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
/**
 *
 * @Block(
 *   id = "annotated_bibliography_export_block",
 *   admin_label = @Translation("Annotated bibliography export block"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */
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

    $node = $this->getContextValue('node');
    $nid_fld = $node->nid->getValue();
    $nid = $nid_fld[0]['value'];

    $build['export_links'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['btn-group btn-group-lg'],
      ],
    ];

    $build['export_links']['link_ris'] = [
      '#type' => 'link',
      '#title' => 'Export RIS',
      '#url' => Url::fromRoute('enrope_bibliography.exportRIS', array('node' => $nid)),
      '#attributes' => [
        'class' => ['btn btn-success mb-4'],
      ],
    ];

    $build['export_links']['link_bib'] = [
      '#type' => 'link',
      '#title' => 'Export Bibtex',
      '#url' => Url::fromRoute('enrope_bibliography.exportBIB', array('node' => $nid)),
      '#attributes' => [
        'class' => ['btn btn-success mb-4'],
      ],
    ];

    return $build;
  }
}
