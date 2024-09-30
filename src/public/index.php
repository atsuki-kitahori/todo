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

// フィルタリングと並べ替えの処理
$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'created_at_desc';

$query =
    'SELECT tasks.*, categories.name as category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.user_id = ?';
$params = [$_SESSION['user_id']];

if ($keyword) {
    $query .= ' AND tasks.contents LIKE ?';
    $params[] = "%$keyword%";
}

if ($category) {
    $query .= ' AND tasks.category_id = ?';
    $params[] = $category;
}

if ($status !== '') {
    $query .= ' AND tasks.status = ?';
    $params[] = $status;
}

switch ($sort) {
    case 'created_at_asc':
        $query .= ' ORDER BY tasks.created_at ASC';
        break;
    case 'created_at_desc':
    default:
        $query .= ' ORDER BY tasks.created_at DESC';
        break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <form action="" method="get" class="mb-4">
            <div class="bg-white p-4 rounded-lg shadow-md max-w-3xl mx-auto">
                <h3 class="text-lg font-semibold mb-2 text-left">絞り込み検索</h3>
                <div class="flex flex-wrap justify-center items-center gap-4">
                    <input type="text" name="keyword" placeholder="キーワードを入力" value="<?= htmlspecialchars(
                        $keyword
                    ) ?>" class="border p-2 rounded w-64">
                    <div class="flex flex-col items-start gap-2">
                        <div>
                            <input type="radio" id="sort_new" name="sort" value="created_at_desc" <?= $sort ===
                            'created_at_desc'
                                ? 'checked'
                                : '' ?>>
                            <label for="sort_new">新着順</label>
                        </div>
                        <div>
                            <input type="radio" id="sort_old" name="sort" value="created_at_asc" <?= $sort ===
                            'created_at_asc'
                                ? 'checked'
                                : '' ?>>
                            <label for="sort_old">古い順</label>
                        </div>
                    </div>
                    <select name="category" class="border p-2 rounded">
                        <option value="">カテゴリ</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $category ==
$cat['id']
    ? 'selected'
    : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="flex flex-col items-start gap-2">
                        <div>
                            <input type="radio" id="status_completed" name="status" value="1" <?= $status ===
                            '1'
                                ? 'checked'
                                : '' ?>>
                            <label for="status_completed">完了</label>
                        </div>
                        <div>
                            <input type="radio" id="status_incomplete" name="status" value="0" <?= $status ===
                            '0'
                                ? 'checked'
                                : '' ?>>
                            <label for="status_incomplete">未完了</label>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">検索</button>
                </div>
            </div>
        </form>
        <div class="mb-4">
            <a href="task/create.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 inline-block">タスクを追加</a>
        </div>
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">タスク名</th>
                    <th class="p-3 text-left">締め切り</th>
                    <th class="p-3 text-left">カテゴリー名</th>
                    <th class="p-3 text-left">完了未完了</th>
                    <th class="p-3 text-left">編集</th>
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
                        <td class="p-3">
                            <a href="task/updateStatus.php?id=<?= $task[
                                'id'
                            ] ?>&status=<?= $task['status'] == 0
    ? 1
    : 0 ?>" class="text-blue-500 hover:underline">
                                <?= $task['status'] == 0 ? '未完了' : '完了' ?>
                            </a>
                        </td>
                        <td class="p-3">
                            <a href="task/edit.php?id=<?= $task[
                                'id'
                            ] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">編集</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>