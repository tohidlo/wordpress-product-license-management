<?php

$allowed_domains = ['example.com', 'testsite.com'];

$domain = $_GET['domain'] ?? '';

if (in_array($domain, $allowed_domains)) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'invalid']);
}
