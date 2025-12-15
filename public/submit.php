<?php
require_once __DIR__ . '/../src/functions.php';
csrf_check();

$data = [
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'message' => trim($_POST['message'] ?? ''),
];

$errors = validate_submission($data);
if ($errors) {
    set_flash('error', implode(' ', $errors));
    header('Location: index.php');
    exit;
}

try {
    $id = create_submission($data);
    set_flash('success', 'Збережено. ID: ' . $id);
} catch (Throwable $e) {
    set_flash('error', 'Помилка збереження: ' . $e->getMessage());
}
header('Location: index.php');
