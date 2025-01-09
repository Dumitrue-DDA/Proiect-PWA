<?php
// index.php va fi pagina default a site-ului
// Cand utilizatorul va intra pe aceasta pagina, va fi trimis la pagina de login
session_start();

// Daca este deja logat, il trimitem pe pagina aplicatiei
if (isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit;
}

// daca nu , il trimitem la pagina de login
header("Location: login.php");
exit;
?>