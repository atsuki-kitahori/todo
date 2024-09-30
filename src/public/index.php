<?php
session_start();

// ユーザーがログインしていない場合、signin.phpにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: user/signin.php');
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

// タスクの取得
$stmt = $pdo->prepare(
    'SELECT tasks.*, categories.name as category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.user_id = ? ORDER BY tasks.deadline ASC'
);
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todoアプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Todoアプリ</h1>
            <nav>
                <a href="index.php" class="text-blue-500 hover:underline mr-4">ホーム</a>
                <a href="category/index.php" class="text-blue-500 hover:underline mr-4">カテゴリ一覧</a>
                <a href="user/logout.php" class="text-blue-500 hover:underline">ログアウト</a>
            </nav>
        </div>
    </header>
    <main class="container mx-auto mt-8">
        <h2 class="text-xl font-bold mb-4">タスク一覧</h2>
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">タスク名</th>
                    <th class="p-3 text-left">締め切り</th>
                    <th class="p-3 text-left">カテゴリー名</th>
                    <th class="p-3 text-left">完了未完了</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr class="border-b">
                        <td class="p-3"><?= htmlspecialchars(
                            $task['contents']
                        ) ?></td>
                        <td class="p-3"><?= htmlspecialchars(
                            $task['deadline']
                        ) ?></td>
                        <td class="p-3"><?= htmlspecialchars(
                            $task['category_name'] ?? '未分類'
                        ) ?></td>
                        <td class="p-3"><?= $task['status'] == 0
                            ? '未完了'
                            : '完了' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
