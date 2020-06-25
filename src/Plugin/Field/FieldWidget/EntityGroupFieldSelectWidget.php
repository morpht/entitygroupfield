<?php

namespace Drupal\entitygroupfield\Plugin\Field\FieldWidget;

/**
 * Plugin implementation of the 'entitygroupfield_select_widget' widget.
 *
 * @FieldWidget(
 *   id = "entitygroupfield_select_widget",
 *   label = @Translation("Group select"),
 *   field_types = {
 *     "group_content"
 *   }
 * )
 */
class EntityGroupFieldSelectWidget extends EntityGroupFieldWidgetBase {

  /**
   * {inheritdoc}
   */
  protected function buildAddElement($entity_plugin_id, array $existing_gcontent) {
    // Get the list of all allowed groups, given the circumstances.
    $allowed_groups = $this->getAllowedGroups($entity_plugin_id, $existing_gcontent);

    // If there are no available groups, don't build a form element.
    if (empty($allowed_groups)) {
      return [];
    }

    return [
      '#title' => $this->t('Group'),
      '#type' => 'select',
      '#description' => $this->t('Select a group'),
      '#options' => $allowed_groups,
    ];
  }

  /**
   * Gets a list of groups with a specific plugin installed.
   *
   * @param string $plugin_id
   *   The plugin ID to filter the groups.
   *
   * @return \Drupal\group\Entity\GroupInterface[]
   *   The list of group entities.
   */
  protected function getPluginGroups($plugin_id) {
    return $this->entityTypeManager->getStorage('group')->
      loadByProperties(['type' => $this->getPluginGroupTypes($plugin_id)]);
  }

  /**
   * Gets allowed group options for a select form element.
   *
   * @param string $entity_plugin_id
   *   The plugin ID to get existing content.
   * @param array $existing_gcontent
   *   The existing group content.
   *
   * @return array
   *   Allowed groups options using optgroup for the group types.
   */
  protected function getAllowedGroups($entity_plugin_id, array $existing_gcontent) {
    $groups = $this->getPluginGroups($entity_plugin_id);
    // If there are no groups with the plugin enabled, return early.
    if (empty($groups)) {
      return [];
    }

    $allowed_groups = [];
    $all_restricted = TRUE;
    /** @var \Drupal\Core\Session\AccountInterface $account */
    $account = $this->currentUser->getAccount();

    // Checking cardinality.
    $groups_cardinality = $this->getGroupsCardinality($groups, $entity_plugin_id);
    $excluded_groups = [];
    if ($existing_gcontent) {
      $groups_ammounts = [];
      foreach ($existing_gcontent as $gcontent) {
        // Do not count the content if it was removed.
        if ($gcontent['mode'] == 'removed') {
          continue;
        }
        if (isset($gcontent['entity'])) {
          $gid = $gcontent['entity']->gid->getString();
          $groups_ammounts[$gid] = isset($groups_ammounts[$gid]) ? $groups_ammounts[$gid] + 1 : 1;
          if ($groups_ammounts[$gid] >= $groups_cardinality[$gid]) {
            $excluded_groups[] = $gid;
          }
        }
      }
    }

    /** @var \Drupal\group\Entity\Group $group */
    foreach ($groups as $group) {
      if (in_array($group->id(), $excluded_groups)) {
        continue;
      }
      // Check creation permissions.
      $can_create = FALSE;
      if ($entity_plugin_id == 'group_membership') {
        $can_create = $group->hasPermission("administer members", $account);
      }
      if (!$can_create) {
        $can_create = $group->hasPermission("create $entity_plugin_id entity", $account);
      }
      if ($can_create) {
        $all_restricted = FALSE;
        $group_bundle = $group->bundle();
        $group_bundle_label = $group->getGroupType()->label();
        $allowed_groups[$group_bundle_label][$group->id()] = $this->entityRepository->getTranslationFromContext($group)->label();
      }
    }

    return $allowed_groups;
  }

}