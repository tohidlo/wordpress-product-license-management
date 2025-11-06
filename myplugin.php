<?php
/*
Plugin Name: myplugin
*/
if (!defined('ABSPATH')) exit;

if (!(function () {

    $server_api = 'http://localhost/server-api.php';
    $secret_key = 'my_secret_key_123';
    $token_option = 'myplugin_license_token';
    $grace_option = 'myplugin_grace_until';

    $grace_hours = 6;
    $retry_interval = 15 * 60;

    $sign = function($start, $last) use ($secret_key) {
        return hash_hmac('sha256', $start . '|' . $last, $secret_key);
    };

    $verify = function($start, $last, $sig) use ($sign) {
        return hash_equals($sign($start, $last), $sig);
    };

    $start_grace = function() use (&$grace_option, &$sign) {
        $start = time();
        update_option($grace_option, $start . '.0.' . $sign($start, 0));
    };

    $clear_grace = function() use (&$grace_option) {
        delete_option($grace_option);
    };

    $update_grace_last = function($new_last) use (&$grace_option, &$sign) {
        $val = get_option($grace_option);
        if (!$val) return false;
        $parts = explode('.', $val);
        if (count($parts) !== 3) return false;
        $start = (int)$parts[0];
        update_option($grace_option, $start . '.' . (int)$new_last . '.' . $sign($start, (int)$new_last));
        return true;
    };

    $request_new_token = function($allow_grace = false) use (&$server_api, &$token_option, &$start_grace, &$clear_grace) {
        $domain = parse_url(home_url(), PHP_URL_HOST);
        $response = wp_remote_get($server_api . '?domain=' . urlencode($domain), ['timeout' => 5]);

        if (is_wp_error($response)) {
            if ($allow_grace) {
                $start_grace();
                return true;
            }
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($data['status']) || $data['status'] !== 'ok' || empty($data['token'])) {
            if ($allow_grace) {
                $start_grace();
                return true;
            }
            return false;
        }

        update_option($token_option, $data['token']);
        $clear_grace();
        return true;
    };

    $token = get_option($token_option);
    if (empty($token)) return $request_new_token(false);

    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    list($base64_header, $base64_payload, $base64_signature) = $parts;

    $payload_json = base64_decode(strtr($base64_payload, '-~', '+/') . str_repeat('=', (4 - strlen($base64_payload) % 4) % 4));
    $payload = json_decode($payload_json, true);
    if (!$payload || empty($payload['exp']) || empty($payload['domain'])) return false;

    $domain = parse_url(home_url(), PHP_URL_HOST);

    $expected_sig_raw = hash_hmac('sha256', $base64_header . '.' . $base64_payload, $secret_key, true);
    $expected_sig = rtrim(strtr(base64_encode($expected_sig_raw), '+/', '-~'), '=');
    if ($expected_sig !== $base64_signature) return false;

    if ($payload['domain'] !== $domain) return false;

    $expired = $payload['exp'] < time();
    $grace_data = get_option($grace_option);

    if ($expired) {
        if ($grace_data) {
            $gp = explode('.', $grace_data);
            if (count($gp) !== 3) return false;

            $start = (int)$gp[0];
            $last = (int)$gp[1];
            $sig = $gp[2];

            if (!$verify($start, $last, $sig)) return false;

            if ($start + ($grace_hours * 3600) > time()) {
                if (time() - $last > $retry_interval) {
                    $update_grace_last(time());
                    $request_new_token(true);
                }
                return true;
            }

            return false;
        }

        return $request_new_token(true);
    }

    $clear_grace();
    return true;

})()) {
    deactivate_plugins(plugin_basename(__FILE__));
    add_action('admin_notices', function() {
        if (current_user_can('manage_options'))
            echo '<div class="notice notice-error"><p>myplugin: License not active</p></div>';
    });
    return;
}