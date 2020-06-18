Entity Group Field

This module provides a computed field that can be configured on any entity types
that are associated with Groups (https://www.drupal.org/project/group), allowing
users with sufficient permissions to view group associations directly while
viewing entities, and to manage group associations while editing entities.

For example, it can be used on 'user' entities to manage group memberships while
editing a user's profile, or to manage what groups a given node is associated
with while editing the node.

By default, the computed field uses the label 'Group memberships' for User
entities, and 'Groups' for everything else. If you need to customize these
labels and/or add descriptions for these fields, you can install and enable the
'Base Field Override UI' contributed module:

https://www.drupal.org/project/base_field_override_ui
