<?php

namespace Genero\Component;

use WPSEO_Options;
use WP_Post;

class HeroComponent implements ComponentInterface
{
    public static $groupKey = 'group_5841c75e2a9b5';

    public function __construct()
    {
        add_action('acf/init', function () {
            if ($this->validateRequirements()) {
                add_filter('wpseo_opengraph_image', [$this, 'setOgImage']);
                // Add options which are not overridable.
                require_once __DIR__ . '/HeroComponent/acf-options-export.php';
            }
        });
    }

    public function addAcfFieldgroup()
    {
        // Only save it if it doesn't exist, otherwise let themers edit it.
        if (!acf_get_field_group(self::$groupKey)) {
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
        if (!empty($hero[0]['slide_image']['url'])) {
            return $hero[0]['slide_image']['url'];
        }
        if (!empty($hero[0]['slide_image'])) {
            return $hero[0]['slide_image'];
        }
        return $image;
    }

    public function validateRequirements()
    {
        return function_exists('get_field');
    }
}
