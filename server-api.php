<?php
header('Content-Type: application/json');

$allowed_domains = ['localhost9', 'testsite.com'];
$secret_key = 'my_secret_key_123';

$domain = $_GET['domain'] ?? '';

if (!in_array($domain, $allowed_domains)) {
    echo json_encode(['status' => 'invalid']);
    exit;
}

$expiration = time() + 86400;

$payload = json_encode([
    'domain' => $domain,
    'exp' => $expiration
]);
$header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
$base64_header = rtrim(strtr(base64_encode($header), '+/', '-~'), '=');
$base64_payload = rtrim(strtr(base64_encode($payload), '+/', '-~'), '=');
$signature = hash_hmac('sha256', $base64_header . '.' . $base64_payload, $secret_key, true);
$base64_signature = rtrim(strtr(base64_encode($signature), '+/', '-~'), '=');

$token = $base64_header . '.' . $base64_payload . '.' . $base64_signature;

echo json_encode([
    'status' => 'ok',
    'token' => $token
]);