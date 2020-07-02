<?php

namespace Drupal\entitygroupfield\Plugin\Field\FieldWidget;

/**
 * Plugin implementation of the deprecated 'group_selector_widget' widget.
 *
 * @deprecated in entitygroupfield:1.0.0 and is removed from
 *   entitygroupfield:2.0.0. Use either 'entitygroupfield_select_widget' or
 *   'entitygroupfield_autocomplete_widget' instead.
 *
 * @FieldWidget(
 *   id = "group_selector_widget",
 *   label = @Translation("(Deprecated) Group selector"),
 *   field_types = {
 *     "entitygroupfield"
 *   }
 * )
 */
class GroupSelectorWidget extends EntityGroupFieldSelectWidget {

}
