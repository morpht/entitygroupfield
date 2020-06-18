# CONTENTS OF THIS FILE
-----------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration


# INTRODUCTION
--------------

Entity Group Field a computed field that can be configured on any entity types
that are associated with Groups (https://www.drupal.org/project/group), allowing
users with sufficient permissions to view group associations directly while
viewing entities, and to manage group associations while editing entities.

For example, it can be used on 'user' entities to manage group memberships while
editing a user's profile, or to manage what groups a given node is associated
with while editing the node.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/entitygroupfield

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/entitygroupfield


# REQUIREMENTS
--------------

This module requires the core 'Group' module:
https://www.drupal.org/project/group

You must also install one or more "Group content enabler" modules (e.g. 'gnode'
from the Group core project).


# INSTALLATION
--------------

Install the Entity Group Field module as you would normally install a
contributed Drupal module. Visit https://www.drupal.org/node/1897420 for further
information.


# CONFIGURATION
---------------

## Initial group configuration

In order for this module to do anything, you must first configure your site to
have group types, create one or more groups, and to use a content enabler to
associate various kinds of content with your groups. For example:

* Enable the 'gnode' module from Group core.
* Create a group type at `/admin/group/types` (e.g. 'Private group').
* Click on the 'Set available content' operation
  (`/admin/group/types/manage/private_group/content`).
* Scroll down to a node type you care about (e.g. 'Article')
* Click the 'Install' button and configure appropriately.

By default, users can automatically be associated with groups.


## Viewing group associations

To view the groups a given entity is associated with, go to the 'Manage display'
page for the entity and bundle you want to configure, enable the 'Groups'
computed field, and configure the field formatter you wish.


### Example: Article nodes

For example, if you have a node type called 'Article' that can belong to groups
(see above), you could do something like this:

* Visit Admin > Structure > Content types (`/admin/structure/types`)
* Click on the 'Manage display' operation next to the 'Article' node type
  (`/admin/structure/types/manage/article/display`)
* The 'Groups' computed field provided by this module is disabled by default.
* Drag it up into the enabled fields you wish to see on your Article nodes.
* Select the appropriate format in the select list.
* Optionally click the gear icon to further configure the formatter.
* Click 'Save' at the bottom of the form.

You can repeat these steps for as many display modes as you need.


### Available field formatters

There are 4 possible field formatters you can use to view the group association
for any given entity:

1. 'Parent group label' shows the label (title) of the group. Generally, this is
   what you want if you only want to see the names of the groups a given entity
   is associated with. Under the formatter settings there's an option to have
   this label rendered as a link to the group.
2. 'Parent group rendered entity' renders the group entity. Under the formatter
   settings there's an option to pick what view mode to render the group with.
3. 'Parent group ID' shows the numeric identifier for the group. Probably not
   what you want, but available if you need it.

4. 'Rendered entity' will show the group content entity itself. This is the
   underlying relationship between any entity and a group (not to be confused
   with the group entity described above under 'Parent group rendered entity').
   Generally this isn't what you want to see, but if your group associations are
   complex, this can be a good option. Under the formatter settings you can pick
   what display mode to render these 'group content' entities with.


## Editing group associations

@todo


## Field labels

By default, the computed field uses the label 'Group memberships' for User
entities, and 'Groups' for everything else. If you need to customize these
labels and/or add descriptions for these computed fields, you can install and
enable the 'Base Field Override UI' contributed module:

https://www.drupal.org/project/base_field_override_ui
