<?php

namespace Drupal\enrope_group\Plugin\Field\FieldType;

use Drupal\Core\Url;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

/**
 * Variant of the 'link' field that links to the current company.
 *
 * @FieldType(
 *   id = "enrope_group_join_leave",
 *   label = @Translation("Join/Leave buttons"),
 *   description = @Translation("Join/Leave buttons"),
 *   default_widget = "link_default",
 *   default_formatter = "link",
 * )
 */
class EnropeGroupJoinLeaveLinkItem extends LinkItem
{

  /**
   * Whether or not the value has been calculated.
   *
   * @var bool
   */
  protected $isCalculated = FALSE;

  /**
   * {@inheritdoc}
   */
  public function __get($name)
  {
    $this->ensureCalculated();
    return parent::__get($name);
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $this->ensureCalculated();
    return parent::isEmpty();
  }

  /**
   * {@inheritdoc}
   */
  public function getValue()
  {
    $this->ensureCalculated();
    return parent::getValue();
  }

  /**
   * Calculates the value of the field and sets it.
   */
  protected function ensureCalculated()
  {
    if (!$this->isCalculated) {
      $entity = $this->getEntity();
      if (!$entity->isNew()) {


        $account = \Drupal::currentUser();

        if ($this->getEntity()->getMember($account)) {
          if ($this->getEntity()->hasPermission('leave group', $account)) {

            $link = new Url('entity.group.leave', ['group' => $this->getEntity()->id()]);
            $link_string = $link->toUriString();
            $value = [
              'uri' => $link_string,
              'title' => t('Leave'),
              'options' => [
                'attributes' => [
                  'class' => [
                    'btn btn-danger'
                  ]
                ],
              ]
            ];
            $this->setValue($value);
          }
        } elseif ($this->getEntity()->hasPermission('join group', $account)) {
          $link = new Url('entity.group.join', ['group' => $this->getEntity()->id()]);
          $link_string = $link->toUriString();
          $value = [
            'uri' => $link_string,
            'title' => t('Join'),
            'options' => [
              'attributes' => [
                'class' => [
                  'btn btn-success'
                ]
              ],
            ]
          ];
          $this->setValue($value);
        }


      }
      $this->isCalculated = TRUE;
    }
  }

}
