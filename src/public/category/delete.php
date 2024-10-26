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

// カテゴリIDの取得
$categoryId = $_GET['id'] ?? '';

if ($categoryId) {
    // カテゴリの所有者を確認
    $stmt = $pdo->prepare('SELECT user_id FROM categories WHERE id = ?');
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category && $category['user_id'] == $_SESSION['user_id']) {
        // カテゴリが使用されているかチェック
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM tasks WHERE category_id = ?'
        );
        $stmt->execute([$categoryId]);
        $taskCount = $stmt->fetchColumn();

        if ($taskCount > 0) {
            $_SESSION['error_message'] =
                '現在タスクで使用されているので削除できません';
        } else {
            // カテゴリの削除
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$categoryId]);
        }
    }
}

// index.phpにリダイレクト
header('Location: index.php');
exit();
