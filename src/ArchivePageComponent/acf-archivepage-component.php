<?php
// @codingStandardsIgnoreFile
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_593ff043dd4db',
	'title' => 'Archive options',
	'fields' => array (
		array (
			'key' => 'field_593ff04d5ca2d',
			'label' => 'Archive for',
			'name' => 'archive__post_type',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array (
				'Post type' => array(
					'post_type:post' => 'Posts'
				),
			),
			'default_value' => array (
			),
			'allow_null' => 1,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array(
			'key' => 'field_5b0d2cbaec202',
			'label' => 'Exclude terms',
			'name' => 'archive__exclude_terms',
			'type' => 'taxonomy',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'category',
			'field_type' => 'multi_select',
			'allow_null' => 0,
			'add_term' => 1,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'show_column' => 0,
			'show_column_weight' => 1000,
			'multiple' => 0,
		),
		array(
			'key' => 'field_593ff08d5ca2e',
			'label' => 'Posts per page',
			'name' => 'archive__posts_per_page',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5a9911b97c11a',
			'label' => 'Parents only',
			'name' => 'archive__parents_only',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
			'show_column' => 0,
			'show_column_sortable' => 0,
			'show_column_weight' => 1000,
			'allow_quickedit' => 0,
			'allow_bulkedit' => 0,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'page',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'side',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

endif;
