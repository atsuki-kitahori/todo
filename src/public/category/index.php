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

// カテゴリーの取得
$stmt = $pdo->prepare('SELECT * FROM categories WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カテゴリ管理 - Todoアプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8 max-w-2xl">
        <h1 class="text-2xl font-bold mb-4">カテゴリ管理</h1>
        
        <!-- カテゴリ登録フォーム -->
        <form action="store.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="category_name">
                    カテゴリ名
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category_name" type="text" name="category_name" >
            </div>
            <?php if (isset($_SESSION['error_message'])): ?>
                <p class="text-red-500 mb-4"><?= htmlspecialchars(
                    $_SESSION['error_message']
                ) ?></p>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    カテゴリを追加
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="../index.php">
                    戻る
                </a>
            </div>
        </form>

        <!-- カテゴリ一覧 -->
        <h2 class="text-xl font-bold mb-2">カテゴリ一覧</h2>
        <ul class="bg-white shadow-md rounded px-8 pt-6 pb-8">
            <?php foreach ($categories as $category): ?>
                <li class="mb-4 flex justify-between items-center">
                    <span><?= htmlspecialchars($category['name']) ?></span>
                    <div>
                        <a href="edit.php?id=<?= $category[
                            'id'
                        ] ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded mr-2">編集</a>
                        <a href="delete.php?id=<?= $category[
                            'id'
                        ] ?>" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" onclick="return confirm('本当に削除しますか？');">削除</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
