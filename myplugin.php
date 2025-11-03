<?php
/*
Plugin Name: myplugin
*/

if (!defined('ABSPATH')) exit;

if (!(function() {
    $server_api = 'http://localhost/server-api.php';
    $current_domain = parse_url(home_url(), PHP_URL_HOST);

    $response = wp_remote_get($server_api . '?domain=' . $current_domain, ['timeout' => 5]);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if (empty($data['status']) || $data['status'] !== 'ok') {
        return false;
    }

    return true;
})()) {
    deactivate_plugins(plugin_basename(__FILE__));
    add_action('admin_notices', function() {
        if (current_user_can('manage_options')) {
            echo '<div class="notice notice-error"><p>myplugin: License not active</p></div>';
        }
    });
    return;
}