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

$task_id = $_GET['id'] ?? '';
$task = null;
$categories = [];
$error_messages = [];

if ($task_id) {
    // タスクの取得
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        header('Location: ../index.php');
        exit();
    }

    // カテゴリーの取得
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_SESSION['error_messages'])) {
    $error_messages = $_SESSION['error_messages'];
    unset($_SESSION['error_messages']);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク編集 - Todoアプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8 max-w-md">
        <h1 class="text-2xl font-bold mb-4">タスク編集</h1>
        <form action="update.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <input type="hidden" name="id" value="<?= htmlspecialchars(
                $task['id']
            ) ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="contents">
                    タスク名
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="contents" type="text" name="contents" value="<?= htmlspecialchars(
                    $task['contents']
                ) ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="deadline">
                    締め切り
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="deadline" type="date" name="deadline" value="<?= htmlspecialchars(
                    $task['deadline']
                ) ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="category_id">
                    カテゴリー
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category_id" name="category_id">
                    <option value="">カテゴリーなし</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $task[
    'category_id'
] == $category['id']
    ? 'selected'
    : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($error_messages)): ?>
                <?php foreach ($error_messages as $error): ?>
                    <p class="text-red-500 mb-4"><?= htmlspecialchars(
                        $error
                    ) ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    更新
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="../index.php">
                    戻る
                </a>
            </div>
        </form>
    </div>
</body>
</html>
