<?php

declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/firebase_database.php';

function validate_submission(array $d): array
{
    $err = [];
    $name = trim($d['name'] ?? '');
    $email = trim($d['email'] ?? '');
    $msg = trim($d['message'] ?? '');
    if ($name === '' || mb_strlen($name) < 2) $err['name'] = 'Імʼя мін. 2 символи.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err['email'] = 'Некоректний email.';
    if ($msg === '' || mb_strlen($msg) < 3) $err['message'] = 'Повідомлення мін. 3 символи.';
    return $err;
}

function create_submission(array $d): string
{
    $db = database();
    $data = [
        'name' => $d['name'],
        'email' => $d['email'] ?: null,
        'message' => $d['message'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    
    $ref = $db->getReference('guestbook')->push($data);
    return $ref->getKey();
}

function list_submissions(int $limit = 20): array
{
    $db = database();
    $snapshot = $db->getReference('guestbook')->getSnapshot();
    
    $rows = [];
    $data = $snapshot->getValue() ?? [];
    
    // Convert to array and reverse to show newest first
    $data = array_reverse($data, true);
    
    // Apply limit
    $data = array_slice($data, 0, $limit, true);
    
    foreach ($data as $key => $value) {
        $rows[] = [
            'id' => $key,
            'name' => (string)($value['name'] ?? ''),
            'email' => (string)($value['email'] ?? ''),
            'message' => (string)($value['message'] ?? ''),
            'created_at' => (string)($value['created_at'] ?? ''),
        ];
    }
    return $rows;
}
