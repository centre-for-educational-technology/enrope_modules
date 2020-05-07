<?php

namespace Drupal\group\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Group membership state configuration entity.
 *
 * @ConfigEntityType(
 *   id = "group_membership_state",
 *   label = @Translation("Group membership state"),
 *   label_singular = @Translation("group membership state"),
 *   label_plural = @Translation("group membership states"),
 *   label_count = @PluralTranslation(
 *     singular = "@count group membership state",
 *     plural = "@count group membership states"
 *   ),
 *   handlers = {
 *     "storage" = "Drupal\group\Entity\Storage\GroupMembershipStateStorage",
 *     "access" = "Drupal\group\Entity\Access\GroupMembershipStateAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\group\Entity\Form\GroupMembershipStateForm",
 *       "edit" = "Drupal\group\Entity\Form\GroupMembershipStateForm",
 *       "delete" = "Drupal\group\Entity\Form\GroupMembershipStateDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\group\Entity\Routing\GroupMembershipStateRouteProvider",
 *     },
 *     "list_builder" = "Drupal\group\Entity\Controller\GroupMembershipStateListBuilder",
 *   },
 *   admin_permission = "administer group",
 *   config_prefix = "membership_state",
 *   static_cache = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "weight" = "weight",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/group/types/manage/{group_type}/membership_states/add",
 *     "collection" = "/admin/group/types/manage/{group_type}/membership_states",
 *     "delete-form" = "/admin/group/types/manage/{group_type}/membership_states/{group_membership_state}/delete",
 *     "edit-form" = "/admin/group/types/manage/{group_type}/membership_states/{group_membership_state}"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "weight",
 *     "internal",
 *     "state",
 *     "group_type"
 *   }
 * )
 */
class GroupMembershipState extends ConfigEntityBase implements GroupMembershipStateInterface {

  /**
   * The machine name of the group membership state.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the group membership state.
   *
   * @var string
   */
  protected $label;

  /**
   * The weight of the group membership state in administrative listings.
   *
   * @var int
   */
  protected $weight;

  /**
   * Whether the group membership state is used internally.
   *
   * Internal states cannot be edited or assigned directly. They do not show in
   * the list of group membership states to edit or assign. Examples of these
   * are the special group membership states
   * 'active', 'pending' and 'banned'.
   *
   * @var bool
   */
  protected $internal = FALSE;

  /**
   * The membership state.
   *
   * Supported values are: 'active', 'pending' and 'banned'.
   *
   * @var string
   */
  protected $state = 'active';

  /**
   * The ID of the group type this role belongs to.
   *
   * @var string
   */
  protected $group_type;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->get('weight');
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->set('weight', $weight);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return $this->internal;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    return $this->state == 'active';
  }

  /**
   * {@inheritdoc}
   */
  public function isPending() {
    return $this->state == 'pending';
  }

  /**
   * {@inheritdoc}
   */
  public function isBanned() {
    return $this->state == 'banned';
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupType() {
    return GroupType::load($this->group_type);
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupTypeId() {
    return $this->group_type;
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    $uri_route_parameters['group_type'] = $this->getGroupTypeId();
    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();
    $this->addDependency('config', $this->getGroupType()->getConfigDependencyName());
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    parent::postLoad($storage, $entities);
    // Sort the queried roles by their weight.
    // See \Drupal\Core\Config\Entity\ConfigEntityBase::sort().
    uasort($entities, 'static::sort');
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    if (!isset($this->weight) && ($group_membership_states = $storage->loadMultiple())) {
      // Set a state weight to make this new state last.
      $max = array_reduce($group_membership_states, function ($max, $group_membership_state) {
        return $max > $group_membership_state->weight ? $max : $group_membership_state->weight;
      });

      $this->weight = $max + 1;
    }
  }

}
