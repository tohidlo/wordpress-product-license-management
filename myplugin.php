<?php
/*
Plugin Name: myplugin
*/
if (!defined('ABSPATH')) exit;


if (!(function() {
    $server_api = 'http://localhost/server-api.php';
    $secret_key = 'my_secret_key_123';
    $token_option = 'myplugin_license_token';
    $expiration_seconds = 86400;

    $request_new_token = function() use (&$server_api, &$token_option) {
        $current_domain = parse_url(home_url(), PHP_URL_HOST);
        $response = wp_remote_get($server_api . '?domain=' . urlencode($current_domain), ['timeout' => 5]);
        
        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data['status']) || $data['status'] !== 'ok' || empty($data['token'])) {
            return false;
        }

        update_option($token_option, $data['token']);

        return true;
    };

    $token = get_option($token_option);

    if (empty($token)) {
        return $request_new_token();
    }

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return $request_new_token();
    }

    list($base64_header, $base64_payload, $base64_signature) = $parts;

    $payload_json = base64_decode(strtr($base64_payload, '-~', '+/') . str_repeat('=', (4 - strlen($base64_payload) % 4) % 4));
    $payload = json_decode($payload_json, true);
    if (!$payload || !isset($payload['exp']) || !isset($payload['domain'])) {
        return $request_new_token();
    }

    if ($payload['exp'] < time()) {
        return $request_new_token();
    }

    $expected_signature = hash_hmac('sha256', $base64_header . '.' . $base64_payload, $secret_key, true);
    $expected_base64 = rtrim(strtr(base64_encode($expected_signature), '+/', '-~'), '=');

    if ($expected_base64 !== $base64_signature) {
        return $request_new_token();
    }

    $current_domain = parse_url(home_url(), PHP_URL_HOST);
    if ($payload['domain'] !== $current_domain) {
        return $request_new_token();
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