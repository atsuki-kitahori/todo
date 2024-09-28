<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todoアプリ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #f8f9fa;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        nav a {
            color: #333;
            text-decoration: none;
            margin-left: 1rem;
        }
        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Todoアプリ</div>
        <nav>
            <a href="index.php">ホーム</a>
            <a href="../category/index.php">カテゴリ一覧</a>
            <a href="../user/logout.php">ログアウト</a>
        </nav>
    </header>
    <main>
        <?php echo 'Welcome TECH QUEST!'; ?>
    </main>
</body>
</html>
