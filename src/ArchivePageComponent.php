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

            list($object_type, $object_name) = explode(':', $archive . ':');

            // Backward compability
            if (empty($object_name)) {
                $object_name = $object_type;
                $object_type = 'post_type';
            }

            switch ($object_type) {
                case 'post_type':
                    array_splice($templates, 2, 0, ["archive-${object_name}.php", "archive.php"]);
                    break;

                case 'taxonomy':
                    array_splice($templates, 2, 0, ["terms-${object_name}.php", "terms.php"]);
                    break;
            }
        }
        return $templates;
    }

    public function add_timber_context($context)
    {
        $object = get_queried_object();
        if (!isset($object->post_type) || $object->post_type != 'page') {
            return $context;
        }

        if ($archive = get_field('archive__post_type', $object->ID)) {

            list($object_type, $object_name) = explode(':', $archive . ':');

            // Backward compability
            if (empty($object_name)) {
                $object_name = $object_type;
                $object_type = 'post_type';
            }

            switch ($object_type) {
                case 'post_type':
                    $context['posts'] = new Timber\PostQuery([
                        'post_type' => $object_name,
                        'posts_per_page' => get_field('archive__posts_per_page', $object->ID),
                        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                        'post_parent' => get_field('archive__parents_only', $object->ID) ? 0 : '',
                        'tax_query' => [
                            [
                                'taxonomy' => 'category',
                                'field' => 'term_id',
                                'terms' => get_field('archive__exclude_terms', $object->ID),
                                'operator' => 'NOT IN',
                            ],
                        ],
                    ]);
                    break;

                case 'taxonomy':
                    $context['terms'] = Timber::get_terms([
                        'taxonomy' => $object_name,
                        'hide_empty' => true,
                        'parent' => get_field('archive__parents_only', $object->ID) ? 0 : '',
                        'exclude' => get_field('archive__exclude_terms', $object->ID),
                    ]);
                    break;
            }

            // Override `archive__post_type` as it is used as a CSS class
            $context['post']->archive__post_type = $object_name;
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
            $field['choices']['Post type']['post_type:' . $post_type->name] = $post_type->label;
        }

        $taxonomies = get_taxonomies([
            'public' => true,
            'show_ui' => true,
        ], 'objects');

        foreach ($taxonomies as $taxonomy) {
            $field['choices']['Taxonomy']['taxonomy:' . $taxonomy->name] = $taxonomy->label;
        }

        return $field;
    }

    public function validateRequirements()
    {
        return function_exists('get_field');
    }
}
