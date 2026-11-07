<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}


function csrf_protect() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        die('Security check failed (invalid or expired form token). Please go back, refresh the page and try again.');
    }
}


function post_btn($url, array $fields, $label, $class, $confirm = '') {
    $html = '<form method="POST" action="' . htmlspecialchars($url, ENT_QUOTES) . '" class="d-inline"';
    if ($confirm !== '') {
        $html .= ' onsubmit="return confirm(' . htmlspecialchars(json_encode($confirm), ENT_QUOTES) . ');"';
    }
    $html .= '>' . csrf_field();
    foreach ($fields as $name => $value) {
        $html .= '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES) . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES) . '">';
    }
    $html .= '<button type="submit" class="' . htmlspecialchars($class, ENT_QUOTES) . '">' . htmlspecialchars($label) . '</button></form>';
    return $html;
}
