<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $id_usuario = $_SESSION['id'];

    // Garante que o agendamento pertence ao usuário logado
    $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ? AND id_cliente = ?");
    $stmt->execute([$id, $id_usuario]);
}

header("Location: meus_agendamentos.php");
exit;
?>
