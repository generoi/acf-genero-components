<?php

namespace Genero\Component;

use Timber;

/**
 * A re-usable ACF section component.
 */
class SectionComponent implements ComponentInterface
{
    public static $groupKeys = [
        'group_5845730481011', // Section
        'group_584c3280ba455', // Section: Text
        'group_584c18d4beacd', // Section: Post listing
        'group_584c35da444dd', // Section: Blockquote
        'group_584c1a85a57a8', // Section Field: Title
        'group_584c33545eb9a', // Section Field: Read more
        'group_584c1f53eab6a', // Section Field: Column
        'group_584c1c1b39cc5', // Section Field: Background
    ];

    private static $requirements = [
        'Advanced Custom Fields: Image Crop Add-on' => [
            'class' => 'acf_field_image_crop',
            'url' => 'https://wordpress.org/plugins/acf-image-crop-add-on/',
        ],
        'Advanced Custom Fields: Number Slider' => [
            'class' => 'acf_field_number_slider',
            'url' => 'https://wordpress.org/plugins/advanced-custom-fields-number-slider/',
        ],
        'Advanced Custom Fields: Smart Button' => [
            'class' => 'acf_field_smart_button',
            'url' => 'https://packagist.org/packages/gillesgoetsch/acf-smart-button',
        ],
        'Advanced Custom Fields: Post Type Chooser (genero fork)' => [
            'class' => 'acf_field_post_type_chooser',
            'url' => 'https://github.com/generoi/acf-post-type-chooser',
        ],
        'ACF Widgets' => [
            'class' => 'ACFW_Widget',
            'url' => 'https://github.com/Daronspence/acf-widgets',
        ],
    ];

    private static $widgets = [
        'text' => [
            'title' => 'Text',
            'description' => 'A text widget.',
            'slug' => 'text',
            'id' => 'text_widget',
        ],
        'text_image' => [
            'title' => 'Text with Image',
            'description' => 'A text widget with an image on the side of it.',
            'slug' => 'text_image',
            'id' => 'text_image_widget',
        ],
        'blockquote' => [
            'title' => 'Blockquote',
            'description' => 'A text widget styled to highlight content.',
            'slug' => 'blockquote',
            'id' => 'blockquote_widget',
        ],
        'post_listing' => [
            'title' => 'Post Listing',
            'description' => 'A widget displaying a list of post teasers',
            'slug' => 'post_listing',
            'id' => 'post_listing_widget',
        ],
    ];

    public function init()
    {
        add_filter('acf/init', [$this, 'addAcfFieldgroup']);
        add_filter('acfw_include_widgets', [$this, 'registerPostListingWidget']);
        add_filter('acfw_include_widgets', [$this, 'registerBlockquoteWidget']);
        add_filter('acfw_include_widgets', [$this, 'registerTextWidget']);
        add_filter('acfw_include_widgets', [$this, 'registerTextImageWidget']);
    }

    public function addAcfFieldgroup()
    {
        $groups = [];
        foreach (self::$groupKeys as $key) {
            if (!_acf_get_field_group_by_key($key)) {
                $groups[] = $key;
            }
        }

        // Only save it if it doesn't exist, otherwise let themers edit it.
        if (!empty($groups)) {
            $json = __DIR__ . '/SectionComponent/acf-section-component.json';
            AcfFieldLoader::importFieldGroups($json, $groups);
        }
    }

    public function registerTextWidget($widgets)
    {
        $widgets[] = self::$widgets['text'];
        return $widgets;
    }

    public function registerTextImageWidget($widgets)
    {
        $widgets[] = self::$widgets['text_image'];
        return $widgets;
    }

    public function registerBlockquoteWidget($widgets)
    {
        $widgets[] = self::$widgets['blockquote'];
        return $widgets;
    }

    public function registerPostListingWidget($widgets)
    {
        $widgets[] = self::$widgets['post_listing'];
        return $widgets;
    }

    public function validateRequirements()
    {
        $notices = [];
        foreach (self::$requirements as $plugin => $info) {
            if (!class_exists($info['class'])) {
                // @codingStandardsIgnoreLine
                $notices[] = '<div class="error"><p><a href="' . $info['url'] .'" target="_blank">' . $plugin. '</a> is required for the Section feature.</p></div>';
            }
        }
        if (!empty($notices)) {
            add_action('admin_notices', function () use ($notices) {
                echo implode('', $notices);
            });
        }
        return empty($notices);
    }
}
