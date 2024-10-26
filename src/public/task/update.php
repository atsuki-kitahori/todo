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
    $task_id = $_POST['id'] ?? '';
    $contents = $_POST['contents'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $category_id = $_POST['category_id'] ?? '';

    $error_messages = [];

    if (empty($contents)) {
        $error_messages[] = 'タスク名が入力されていません';
    }
    if (empty($deadline)) {
        $error_messages[] = '締切日が入力されていません';
    }
    if (empty($category_id)) {
        $error_messages[] = 'カテゴリが選択されていません';
    }

    if (!empty($error_messages)) {
        $_SESSION['error_messages'] = $error_messages;
        header("Location: edit.php?id=$task_id");
        exit();
    }

    // タスク更新処理
    $stmt = $pdo->prepare(
        'UPDATE tasks SET contents = ?, deadline = ?, category_id = ? WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([
        $contents,
        $deadline,
        $category_id,
        $task_id,
        $_SESSION['user_id'],
    ]);

    header('Location: ../index.php');
    exit();
}

// POSTリクエストでない場合は、index.phpにリダイレクト
header('Location: ../index.php');
exit();
