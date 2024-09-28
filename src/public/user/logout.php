<?php
session_start();

// セッション変数を全て解除
$_SESSION = [];

// セッションクッキーを削除
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// セッションを破棄
session_destroy();

// signin.phpにリダイレクト
header('Location: ../signin.php');
exit();
