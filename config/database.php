<?php
$host = 'localhost:3306';
$dbname = 'kauapr27_projetobarbearia';
$username = 'kauapr27_user';
$password = 'cortai@123';     
$charset = 'utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password, $options);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>