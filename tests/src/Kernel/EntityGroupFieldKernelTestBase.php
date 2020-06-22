<?php

namespace Drupal\Tests\entitygroupfield\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Defines an abstract test base for entitygroupfield kernel tests.
 */
abstract class EntityGroupFieldKernelTestBase extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'group',
    'entity',
    'field',
    'options',
    'entitygroupfield',
  ];

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The content enabler plugin manager.
   *
   * @var \Drupal\group\Plugin\GroupContentEnablerManagerInterface
   */
  protected $groupContentPluginManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('system', ['sequences', 'key_value_expire']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('group');
    $this->installEntitySchema('group_content');
    $this->installConfig(['group']);

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->groupContentPluginManager = $this->container->get('plugin.manager.group_content_enabler');

    // Create a default group type.
    $this->createGroupType([
      'id' => 'default',
      'label' => 'Default',
    ]);

  }

  /**
   * Creates a group.
   *
   * @param array $values
   *   (optional) The values used to create the entity.
   *
   * @return \Drupal\group\Entity\Group
   *   The created group entity.
   */
  protected function createGroup(array $values = []) {
    $storage = $this->entityTypeManager->getStorage('group');
    $group = $storage->create($values + [
      'type' => 'default',
      'label' => $this->randomString(),
    ]);
    $group->enforceIsNew();
    $storage->save($group);
    return $group;
  }

  /**
   * Creates a group type.
   *
   * @param array $values
   *   (optional) The values used to create the entity.
   *
   * @return \Drupal\group\Entity\GroupType
   *   The created group type entity.
   */
  protected function createGroupType(array $values = []) {
    $storage = $this->entityTypeManager->getStorage('group_type');
    $group_type = $storage->create($values + [
      'id' => $this->randomMachineName(),
      'label' => $this->randomString(),
    ]);
    $storage->save($group_type);
    return $group_type;
  }

}
