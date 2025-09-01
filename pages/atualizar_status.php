<?php
session_start();
require_once '../config/database.php';

// Verifica se o usuário está logado e é um profissional
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'profissional') {
    header('Location: ../public/home.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendamento_id']) && isset($_POST['acao'])) {
    $agendamento_id = $_POST['agendamento_id'];
    $acao = $_POST['acao'];
    
    // Verifica se o agendamento pertence a este profissional
    $stmt = $pdo->prepare("SELECT id FROM agendamentos WHERE id = ? AND profissional_id = ?");
    $stmt->execute([$agendamento_id, $_SESSION['usuario']['id']]);
    
    if ($stmt->fetch()) {
        // Atualiza o status conforme a ação
        $novo_status = ($acao === 'confirmar') ? 'confirmado' : 'cancelado';
        
        $stmt = $pdo->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $agendamento_id]);
        
        $_SESSION['mensagem'] = "Agendamento " . $novo_status . " com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Agendamento não encontrado ou não pertence a você.";
    }
}

header('Location: telabarbeiro.php');
exit;
?>