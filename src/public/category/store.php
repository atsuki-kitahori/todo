<?php
session_start();

// ユーザーがログインしていない場合、signin.phpにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/signin.php');
    exit();
}

// データベース接続
try {
    $dbUserName = 'root';
    $dbPassword = 'password';
    $pdo = new PDO(
        'mysql:host=mysql; dbname=todo; charset=utf8mb4',
        $dbUserName,
        $dbPassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    $error_message = 'データベース接続エラー: ' . $e->getMessage();
    error_log($error_message);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'] ?? '';

    if (empty($category_name)) {
        $_SESSION['error_message'] = 'カテゴリ名が入力されていません';
        header('Location: index.php');
        exit();
    }

    // カテゴリ登録処理
    $stmt = $pdo->prepare('INSERT INTO categories (name, user_id) VALUES (?, ?)');
    $stmt->execute([$category_name, $_SESSION['user_id']]);

    header('Location: index.php');
    exit();
}

// POSTリクエストでない場合は、index.phpにリダイレクト
header('Location: index.php');
exit();
