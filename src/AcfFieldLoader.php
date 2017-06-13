<?php

namespace Genero\Component;

use acf_settings_tools;

/**
 * ACF Field loader which only loads field groups if they do not exist.
 */
class AcfFieldLoader
{
    public static function importFieldGroups($json_file, $keys)
    {
        if (!is_admin() || wp_doing_ajax() || defined('XMLRPC_REQUEST') || defined('IFRAME_REQUEST')) {
            return;
        }
        acf_include('admin/settings-tools.php');
        $json = json_decode(file_get_contents($json_file), true);
        if (isset($json['key'])) {
            $json = [$json];
        }

        $save = [];
        foreach ($json as $fieldGroup) {
            if (in_array($fieldGroup['key'], $keys)) {
                $save[] = $fieldGroup;
            }
        }

        // Create a temporary JSON with only the groups in question.
        $temp = tempnam(sys_get_temp_dir(), 'acf-field-loader');
        file_put_contents($temp, acf_json_encode($save));

        $acf_settings_tools = new acf_settings_tools();
        acf_disable_filters();

        $_FILES['acf_import_file'] = [
            'error' => false,
            'name' => basename($json_file),
            'tmp_name' => $temp,
        ];
        $import = $acf_settings_tools->import();
    }
}
