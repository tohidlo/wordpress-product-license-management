<?php
/*
Plugin Name: myplugin
*/

$licmy_allowed_domain = 'example.com';
$licmy_current_domain = parse_url(home_url(), PHP_URL_HOST);

if ($licmy_current_domain !== $licmy_allowed_domain) {
	if (function_exists('deactivate_plugins')) {
        deactivate_plugins(plugin_basename(__FILE__));
    }
    die('License invalid: this plugin can only be used on the registered domain.');
}
