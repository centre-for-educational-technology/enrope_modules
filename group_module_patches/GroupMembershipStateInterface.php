<?php

namespace Drupal\group\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a group state entity.
 */
interface GroupMembershipStateInterface extends ConfigEntityInterface {

  /**
   * Returns the weight.
   *
   * @return int
   *   The weight of this state.
   */
  public function getWeight();

  /**
   * Sets the weight to the given value.
   *
   * @param int $weight
   *   The desired weight.
   *
   * @return \Drupal\group\Entity\GroupMembershipStateInterface
   *   The group state this was called on.
   */
  public function setWeight($weight);

  /**
   * Returns whether the state is tied to a group type.
   *
   * @return bool
   *   Whether the state is tied to a group type.
   */
  public function isInternal();

  /**
   * Returns the group type this state belongs to.
   *
   * @return \Drupal\group\Entity\GroupTypeInterface
   *   The group type this state belongs to.
   */
  public function getGroupType();

  /**
   * Returns the ID of the group type this state belongs to.
   *
   * @return string
   *   The ID of the group type this state belongs to.
   */
  public function getGroupTypeId();

  /**
   * Returns whether a member is active.
   *
   * @return bool
   *   TRUE if the state is active, FALSE otherwise.
   */
  public function isActive();

  /**
   * Returns whether a member is pending.
   *
   * @return bool
   *   TRUE if the state is pending, FALSE otherwise.
   */
  public function isPending();

  /**
   * Returns whether a member is banned.
   *
   * @return bool
   *   TRUE if the state is banned, FALSE otherwise.
   */
  public function isBanned();

}
