<?php

namespace Genero\Component;

use WPSEO_Options;
use WP_Post;

class HeroComponent implements ComponentInterface
{
    public static $groupKey = 'group_5841c75e2a9b5';

    public function __construct()
    {
        if ($this->validateRequirements()) {
            add_filter('wpseo_opengraph_image', [$this, 'setOgImage']);
            // Add options which are not overridable.
            require_once __DIR__ . '/HeroComponent/acf-options-export.php';
        }
    }

    public function addAcfFieldgroup()
    {
        // Only save it if it doesn't exist, otherwise let themers edit it.
        if (!_acf_get_field_group_by_key(self::$groupKey)) {
            $json = __DIR__ . '/HeroComponent/acf-hero-component.json';
            AcfFieldLoader::importFieldGroups($json, [self::$groupKey]);
        }
    }

    public function setOgImage($image)
    {
        $options = WPSEO_Options::get_option('wpseo_social');
        if ($image != $options['og_default_image']) {
            return $image;
        }
        $object = get_queried_object();
        // If it's a real thumbnail, use it.
        if ($object instanceof WP_Post && has_post_thumbnail($object)) {
            return $image;
        }
        // Use the first hero slide's image if available.
        $hero = get_field('hero_slide', $object);
        if (!empty($hero[0]['slide_image'])) {
            return $hero[0]['slide_image'];
        }
        return $image;
    }

    public function validateRequirements()
    {
        $success = function_exists('get_field');
        if (!class_exists('acf_field_image_crop')) {
            add_action('admin_notices', function () {
                // @codingStandardsIgnoreLine
                echo '<div class="error"><p><a href="https://wordpress.org/plugins/acf-image-crop-add-on/" target="_blank">ACF Image Crop</a> is required for the Hero feature.</p></div>';
            });
            $success = false;
        }
        return $success;
    }
}
