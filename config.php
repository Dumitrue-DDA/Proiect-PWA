<?php
// Configurarea accesului la baza de date pentru paginile de login si register
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'pwa';

// $conn va fi folosit pentru query-urile catre baza de date
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// debugging in cazul unor erori
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// daca nu exista baza de date, o cream de la 0
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);