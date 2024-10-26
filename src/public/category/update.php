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
    $category_id = $_POST['id'] ?? '';
    $category_name = $_POST['category_name'] ?? '';

    if (empty($category_name)) {
        $_SESSION['error_message'] = 'カテゴリ名が入力されていません';
        header("Location: edit.php?id=$category_id");
        exit();
    }

    // カテゴリ更新処理
    $stmt = $pdo->prepare(
        'UPDATE categories SET name = ? WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$category_name, $category_id, $_SESSION['user_id']]);

    header('Location: index.php');
    exit();
}

// POSTリクエストでない場合は、index.phpにリダイレクト
header('Location: index.php');
exit();
