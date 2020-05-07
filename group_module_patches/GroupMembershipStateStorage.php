<?php

namespace Drupal\group\Entity\Storage;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\group\Entity\GroupInterface;
use Drupal\group\GroupMembershipLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the storage handler class for group role entities.
 *
 * This extends the base storage class, adding required special handling for
 * loading group role entities based on user and group information.
 */
class GroupMembershipStateStorage extends ConfigEntityStorage implements GroupMembershipStateStorageInterface {

  /**
   * Static cache of a user's group membership state IDs.
   *
   * @var array
   */
  protected $userGroupMembershipStateIds = [];

  /**
   * The group membership loader.
   *
   * @var \Drupal\group\GroupMembershipLoaderInterface
   */
  protected $groupMembershipLoader;

  /**
   * Constructs a GroupMembershipStateStorage object.
   *
   * @param \Drupal\group\GroupMembershipLoaderInterface $group_membership_loader
   *   The group membership loader.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid_service
   *   The UUID service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(GroupMembershipLoaderInterface $group_membership_loader, EntityTypeInterface $entity_type, ConfigFactoryInterface $config_factory, UuidInterface $uuid_service, LanguageManagerInterface $language_manager) {
    parent::__construct($entity_type, $config_factory, $uuid_service, $language_manager);
    $this->groupMembershipLoader = $group_membership_loader;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $container->get('group.membership_loader'),
      $entity_type,
      $container->get('config.factory'),
      $container->get('uuid'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function loadByUserAndGroup(AccountInterface $account, GroupInterface $group, $include_implied = TRUE) {
    $uid = $account->id();
    $gid = $group->id();
    $key = $include_implied ? 'include' : 'exclude';

    if (!isset($this->userGroupMembershipStateIds[$uid][$gid][$key])) {
      $ids = [];

      // Get the ID from the 'group_membership_state' field, without loading the
      // group membership state.
      if ($membership = $this->groupMembershipLoader->load($group, $account)) {
        foreach ($membership->getGroupContent()->group_membership_state as $group_membership_state_ref) {
          $ids[] = $group_membership_state_ref->target_id;
        }
      }

      $this->userGroupRoleIds[$uid][$gid][$key] = $ids;
    }

    return $this->loadMultiple($this->userGroupRoleIds[$uid][$gid][$key]);
  }

  /**
   * {@inheritdoc}
   */
  public function resetUserGroupMembershipStateCache(AccountInterface $account, GroupInterface $group = NULL) {
    $uid = $account->id();
    if (isset($group)) {
      unset($this->userGroupMembershipStateIds[$uid][$group->id()]);
    }
    else {
      unset($this->userGroupMembershipStateIds[$uid]);
    }
  }

}
