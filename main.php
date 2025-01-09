<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Creem directoriul de useri daca nu exista
$user_dir = 'user_pages/' . preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['user_id']);
if (!file_exists($user_dir)) {
    mkdir($user_dir, 0755, true);
    // Create first page for new users
    file_put_contents("$user_dir/My First Page.txt", 'Welcome to your notebook! You can start writing here or you can add a new page to write on.');
}

// luam toate paginile
$pages = [];
foreach (glob("$user_dir/*.txt") as $file) {
    $page_name = basename($file, '.txt');
    $pages[$page_name] = file_get_contents($file);
}

// Daca pagina nu exista
if (empty($pages)) {
    $pages['My First Page'] = 'Welcome to your notebook! You can start writing here or you can add a new page to write on.';
    file_put_contents("$user_dir/My First Page.txt", $pages['My First Page']);
}

$text_display = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['content']) && isset($_POST['current_page'])) {
        $page_name = $_POST['current_page'];
        $sanitized_name = str_replace(['/', '\\', '.php'], '', $page_name);
        file_put_contents("$user_dir/$sanitized_name.txt", $_POST['content']);
        $pages[$page_name] = $_POST['content'];
    }

    if (isset($_POST['new_page'])) {
        $new_page = $_POST['new_page'];
        $sanitized_name = str_replace(['/', '\\', '.php'], '', $new_page);
        file_put_contents("$user_dir/$sanitized_name.txt", '');
        $pages[$new_page] = '';
    }

    if (isset($_POST['delete_page']) && count($pages) > 1) {
        $page_to_delete = $_POST['delete_page'];
        $sanitized_name = str_replace(['/', '\\', '.php'], '', $page_to_delete);
        unlink("$user_dir/$sanitized_name.txt");
        unset($pages[$page_to_delete]);
    }

    if (isset($_POST['view_text'])) {
        $text_display = true;
    }
    if (isset($_POST['view_code'])) {
        $text_display = false;
    }
}

$current_page = isset($_GET['page']) ? $_GET['page'] : array_key_first($pages);
?>

<html>

<head>
    <title>Notebook</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #1a1a1a;
    }

    #TextCod {
        width: 100%;
        height: calc(100% - 100px);
    }


    #TextView {
        width: 100%;
        height: calc(100% - 100px);
    }

    .container {
        display: flex;
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
        height: calc(100% - 40px);
    }

    nav {
        width: 200px;
        background-color: #2d2d2d;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    nav a {
        display: block;
        padding: 10px;
        margin-bottom: 5px;
        text-decoration: none;
        color: #e0e0e0;
        border-radius: 5px;
    }

    nav a:hover {
        background-color: #363636;
    }

    nav a.active {
        background-color: #3d3d3d;
        font-weight: bold;
    }

    main {
        flex-grow: 1;
        background-color: #2d2d2d;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

        button {
            margin: 10px 5px;
        }
    }

    textarea {
        width: 100%;
        height: calc(100% - 100px);
        padding: 10px;
        background-color: #2d2d2d;
        color: #e0e0e0;
        border: none;
        resize: none;
        overflow-y: auto;
        box-sizing: border-box;
    }

    .buttons {
        margin-top: 10px;
        height: 40px;
    }

    button {
        padding: 8px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        transition: linear 200ms;
    }

    button:hover {
        transform: scale(1.1);
    }

    .new-page-form {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #ddd;
    }

    .new-page-form input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
</style>

<body>
    <div class="container">
        <nav>
            <?php foreach ($pages as $page => $content): ?>
                <a href="?page=<?= urlencode($page) ?>" class="<?= $current_page === $page ? 'active' : '' ?>">
                    <?= htmlspecialchars($page) ?>
                </a>
            <?php endforeach; ?>

            <div class="new-page-form">
                <form method="POST">
                    <input type="text" name="new_page" placeholder="New page name">
                    <button type="submit">Add Page</button>
                </form>
                <form method="POST" action="logout.php">
                    <button type="submit" style="background-color: red;margin-top:30px">
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <main>

            <form method="POST">
                <button type="submit" name="view_code">View Code</button>
                <button type="submit" name="view_text">View Text</button>
                <input type="hidden" name="current_page" value="<?= htmlspecialchars($current_page) ?>">
                <textarea id="TextCod" name="content" <?php if ($text_display) {
                    echo "hidden";
                } ?>><?= htmlspecialchars($pages[$current_page]) ?></textarea>
                <div id="TextView" <?php if (!$text_display) {
                    echo "hidden";
                } ?>>
                    <?php echo $_POST["content"]; ?>
                </div>
                <div class="buttons">
                    <button type="submit">Save</button>
                    <?php if (count($pages) > 1): ?>
                        <button type="submit" name="delete_page" value="<?= htmlspecialchars($current_page) ?>"
                            onclick="return confirm('Are you sure you want to delete this page?')">
                            Delete Page
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </main>
    </div>
</body>

</html>