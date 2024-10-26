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

$category_id = $_GET['id'] ?? '';
$category_name = '';
$error_message = '';

if ($category_id) {
    $stmt = $pdo->prepare(
        'SELECT * FROM categories WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$category_id, $_SESSION['user_id']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $category_name = $category['name'];
    } else {
        header('Location: index.php');
        exit();
    }
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カテゴリ編集 - Todoアプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8 max-w-md">
        <h1 class="text-2xl font-bold mb-4">カテゴリ編集</h1>
        <form action="update.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <input type="hidden" name="id" value="<?= htmlspecialchars(
                $category_id
            ) ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="category_name">
                    カテゴリ名
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category_name" type="text" name="category_name" value="<?= htmlspecialchars(
                    $category_name
                ) ?>">
            </div>
            <?php if ($error_message): ?>
                <p class="text-red-500 mb-4"><?= htmlspecialchars(
                    $error_message
                ) ?></p>
            <?php endif; ?>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    更新
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="index.php">
                    戻る
                </a>
            </div>
        </form>
    </div>
</body>
</html>