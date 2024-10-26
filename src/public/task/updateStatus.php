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

// タスクIDとステータスの取得
$taskId = $_GET['id'] ?? '';
$newStatus = $_GET['status'] ?? '';

if ($taskId && $newStatus !== '') {
    // タスクの所有者を確認
    $stmt = $pdo->prepare('SELECT user_id FROM tasks WHERE id = ?');
    $stmt->execute([$taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task && $task['user_id'] == $_SESSION['user_id']) {
        // ステータスの更新
        $stmt = $pdo->prepare('UPDATE tasks SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $taskId]);
    }
}

// index.phpにリダイレクト
header('Location: ../index.php');
exit();
