<?php

namespace Genero\Component;

use Timber;

class ArchivePageComponent implements ComponentInterface
{
    protected static $groupKey = 'group_593ff043dd4db';

    public function __construct()
    {
        if ($this->validateRequirements()) {
            add_filter('acf/load_field/name=archive__post_type', [$this, 'post_type_choices'], 9);
            add_filter('page_template_hierarchy', [$this, 'add_template_suggestions'], 9);
            add_action('post_type_archive_link', [$this, 'post_type_archive_link'], 10, 2);
            add_filter('timber/context', [$this, 'add_timber_context'], 9);

            add_filter('manage_pages_columns', [$this, 'add_page_column']);
            add_action('manage_pages_custom_column', [$this, 'add_page_column_content'], 10, 2);
            add_action('admin_head', [$this, 'admin_head']);
            // Add options which are not overridable.
            require_once __DIR__ . '/ArchivePageComponent/acf-archivepage-component.php';
        }
    }

    public function admin_head() {
        echo '<style>th#is_archive { width: 100px; }</style>';
    }

    public function add_page_column($columns) {
        $columns['is_archive'] = __('Archive', 'acf-genero-components');
        return $columns;
    }

    public function add_page_column_content($column, $post_id) {
        switch ($column) {
            case 'is_archive':
                if ($post_type = get_field('archive__post_type', $post_id)) {
                    echo '<a href="' . admin_url("edit.php?post_type=$post_type") . '">' . $post_type . '</a>';
                }
                break;
        }
    }

    public function post_type_archive_link($link, $post_type)
    {
        $pages = get_pages([
            'meta_key' => 'archive__post_type',
            'meta_value' => $post_type,
            'number' => 1,
        ]);
        if (!empty($pages)) {
            $page = reset($pages);
            return get_permalink($page);
        }
        return $link;
    }

    public function add_template_suggestions($templates) {
        $post = get_post();
        if ($post && $archive = get_field('archive__post_type', $post)) {
            array_splice($templates, 2, 0, ["archive-${archive}.php", "archive.php"]);
        }
        return $templates;
    }

    public function add_timber_context($context)
    {
        $object = get_queried_object();
        if (!isset($object->post_type) || $object->post_type != 'page') {
            return $context;
        }

        if ($post_type = get_field('archive__post_type', $object->ID)) {
            $context['posts'] = new Timber\PostQuery([
                'post_type' => $post_type,
                'posts_per_page' => get_field('archive__posts_per_page', $object->ID),
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
            ]);
        }
        return $context;
    }

    public function post_type_choices($field)
    {
        $post_types = get_post_types([
            'public' => true,
            '_builtin' => false,
        ], 'objects');

        foreach ($post_types as $post_type) {
            $field['choices'][$post_type->name] = $post_type->label;
        }
        return $field;
    }

    public function validateRequirements()
    {
        return function_exists('get_field');
    }
}
