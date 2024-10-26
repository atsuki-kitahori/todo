<?php
session_start();
// データベース接続設定
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
}

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error_message = 'パスワードとメールアドレスを入力してください';
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // ユーザーの検証
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // ログイン成功
            $_SESSION['user_id'] = $user['id'];
            header('Location: ../index.php');
            exit();
        } else {
            // ログイン失敗
            $error_message = 'メールアドレスまたはパスワードが違います';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - Todoアプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold mb-6 text-center">ログイン</h1>
        <?php if ($error_message): ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars(
                $error_message
            ); ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars(
            $_SERVER['PHP_SELF']
        ); ?>" method="post">
            <div class="mb-4">
                <input type="email" id="email" name="email" placeholder="Email" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <input type="password" id="password" name="password" placeholder="Password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                ログイン
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="signup.php" class="text-blue-500 hover:underline">会員登録へ</a>
        </div>
    </div>
</body>
</html>
