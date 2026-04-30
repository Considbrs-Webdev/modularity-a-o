<?php

if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
        'key' => 'group_7c1d2e3f4a5b6',
        'title' => __('A-Ö Links', 'modularity-a-o'),
        'fields' => array(
            0 => array(
                'key' => 'field_7c1d2e3f4a5b7a',
                'label' => __('Dynamic source', 'modularity-a-o'),
                'name' => 'source_group',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => __(
                    'Published pages below the selected root are listed on each page view (the root itself is not listed). If a manual row uses the same page as one in that tree, the row updates that entry.',
                    'modularity-a-o'
                ),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    0 => array(
                        'key' => 'field_7c1d2e3f4a5b7b',
                        'label' => __('Root page', 'modularity-a-o'),
                        'name' => 'root_page',
                        'aria-label' => '',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array('page'),
                        'taxonomy' => array(),
                        'allow_null' => 1,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                ),
            ),
            1 => array(
                'key' => 'field_7c1d2e3f4a5b61',
                'label' => __('Manual links', 'modularity-a-o'),
                'name' => 'links',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => __(
                    'Choose Page (internal) or External URL per row—not both. Label is optional (page title or URL is used when empty).',
                    'modularity-a-o'
                ),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => 'field_7c1d2e3f4a5b8a',
                'button_label' => __('Add link', 'modularity-a-o'),
                'rows_per_page' => 20,
                'sub_fields' => array(
                    0 => array(
                        'key' => 'field_7c1d2e3f4a5b8a',
                        'label' => __('Link type', 'modularity-a-o'),
                        'name' => 'row_type',
                        'aria-label' => '',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'page' => __('Page', 'modularity-a-o'),
                            'external' => __('External URL', 'modularity-a-o'),
                        ),
                        'default_value' => 'page',
                        'return_format' => 'value',
                        'multiple' => 0,
                        'allow_null' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'placeholder' => '',
                        'parent_repeater' => 'field_7c1d2e3f4a5b61',
                    ),
                    1 => array(
                        'key' => 'field_7c1d2e3f4a5b62',
                        'label' => __('Link label', 'modularity-a-o'),
                        'name' => 'label',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => __(
                            'Optional. If empty: page title is used for Page rows; the URL is shown for External rows.',
                            'modularity-a-o'
                        ),
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '33',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'maxlength' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'parent_repeater' => 'field_7c1d2e3f4a5b61',
                    ),
                    2 => array(
                        'key' => 'field_7c1d2e3f4a5b7c',
                        'label' => __('Page', 'modularity-a-o'),
                        'name' => 'page',
                        'aria-label' => '',
                        'type' => 'post_object',
                        'instructions' => __(
                            'Internal link. If this page exists in the dynamic tree, this row updates that entry.',
                            'modularity-a-o'
                        ),
                        'required' => 0,
                        'conditional_logic' => array(
                            0 => array(
                                0 => array(
                                    'field' => 'field_7c1d2e3f4a5b8a',
                                    'operator' => '==',
                                    'value' => 'page',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '34',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array('page'),
                        'taxonomy' => array(),
                        'allow_null' => 1,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                        'parent_repeater' => 'field_7c1d2e3f4a5b61',
                    ),
                    3 => array(
                        'key' => 'field_7c1d2e3f4a5b63',
                        'label' => __('External URL', 'modularity-a-o'),
                        'name' => 'url',
                        'aria-label' => '',
                        'type' => 'url',
                        'instructions' => __(
                            'Full URL for an external or non-page link.',
                            'modularity-a-o'
                        ),
                        'required' => 0,
                        'conditional_logic' => array(
                            0 => array(
                                0 => array(
                                    'field' => 'field_7c1d2e3f4a5b8a',
                                    'operator' => '==',
                                    'value' => 'external',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'parent_repeater' => 'field_7c1d2e3f4a5b61',
                    ),
                ),
            ),
        ),
        'location' => array(
            0 => array(
                0 => array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-a-o',
                ),
            ),
            1 => array(
                0 => array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/a-o',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}
