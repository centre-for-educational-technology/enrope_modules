<?php

namespace Drupal\enrope_group;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Item list for a computed field that displays the current company.
 *
 * @see \Drupal\enrope_group\Plugin\Field\FieldType\EnropeGroupJoinLeaveLinkItem
 */
class EnropeGroupJoinLeaveLinkItemList extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   */
  protected function computeValue() {

    if (!isset($this->list[0])) {
      $this->list[0] = $this->createItem(0);
    }
  }

  /**
   * Computes the calculated values for this item list.
   *
   * In this example, there is only a single item/delta for this field.
   *
   * The ComputedItemListTrait only calls this once on the same instance; from
   * then on, the value is automatically cached in $this->items, for use by
   * methods like getValue().
   */
  protected function ensurePopulated() {
    if (!isset($this->list[0])) {
      $this->list[0] = $this->createItem(0);
    }
  }

}
