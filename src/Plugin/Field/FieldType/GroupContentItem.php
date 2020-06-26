<?php

namespace Drupal\entitygroupfield\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;

/**
 * Plugin implementation of the 'group_content' field type.
 *
 * @FieldType(
 *   id = "group_content",
 *   label = @Translation("Groups"),
 *   description = @Translation("This is a computed field to relate content with groups"),
 *   no_ui = TRUE,
 *   default_widget = "entitygroupfield_select_widget",
 *   default_formatter = "parent_group_label_formatter",
 *   list_class = "\Drupal\entitygroupfield\Field\EntityGroupFieldItemList",
 * )
 */
class GroupContentItem extends EntityReferenceItem {

}
