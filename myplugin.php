<?php
/*
Plugin Name: myplugin
*/

register_activation_hook(__FILE__, function() {
    $server_api = 'http://localhost/server-api.php';
    $current_domain = parse_url(home_url(), PHP_URL_HOST);

    $response = wp_remote_get($server_api . '?domain=' . $current_domain);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['status']) || $data['status'] !== 'ok') {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('License invalid: this plugin is not allowed on this domain.');
    }
});