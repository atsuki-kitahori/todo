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
    $contents = $_POST['contents'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $category_id = $_POST['category_id'] ?? '';

    $error_messages = [];

    if (empty($contents)) {
        $error_messages[] = 'タスク名を入力してください。';
    }
    if (empty($deadline)) {
        $error_messages[] = '締め切りを入力してください。';
    }
    if (empty($category_id)) {
        $error_messages[] = 'カテゴリを選択してください。';
    }

    if (!empty($error_messages)) {
        $_SESSION['error_messages'] = $error_messages;
        header('Location: create.php');
        exit();
    }

    // エラーがない場合、タスクを追加する処理を続行
    $stmt = $pdo->prepare(
        'INSERT INTO tasks (user_id, contents, deadline, category_id) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$_SESSION['user_id'], $contents, $deadline, $category_id]);

    header('Location: ../index.php');
    exit();
}

// POSTリクエストでない場合は、create.phpにリダイレクト
header('Location: create.php');
exit();
