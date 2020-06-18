CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration


INTRODUCTION
------------

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


REQUIREMENTS
------------

This module requires the core 'Group' module:
https://www.drupal.org/project/entitygroupfield

You must also install one or more "Group content enabler" modules (e.g. 'gnode'
from the Group core project).


INSTALLATION
------------

Install the Entity Group Field module as you would normally install a
contributed Drupal module. Visit https://www.drupal.org/node/1897420 for further
information.


CONFIGURATION
-------------

By default, the computed field uses the label 'Group memberships' for User
entities, and 'Groups' for everything else. If you need to customize these
labels and/or add descriptions for these computed fields, you can install and
enable the 'Base Field Override UI' contributed module:

https://www.drupal.org/project/base_field_override_ui

