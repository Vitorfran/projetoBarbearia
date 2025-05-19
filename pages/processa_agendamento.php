<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro'] = "Método não permitido";
    header('Location: agendamento.php');
    exit;
}

// Validação dos dados
$cliente_id = $_SESSION['usuario']['id'] ?? null;
$profissional_id = $_POST['barbeiro_id'] ?? null;
$servico_id = $_POST['servico_id'] ?? null;
$data = $_POST['data'] ?? '';
$hora = $_POST['hora'] ?? '';

if (!$cliente_id || !$profissional_id || !$servico_id || !$data || !$hora) {
    $_SESSION['erro'] = "Todos os campos são obrigatórios";
    header('Location: agendamento.php');
    exit;
}

try {
    // Verifica se o profissional está disponível
    $data_hora_inicio = $data . ' ' . $hora . ':00';
    
    $stmt = $pdo->prepare("
        SELECT 1 
        FROM indisponibilidades 
        WHERE profissional_id = ? 
        AND ? BETWEEN data_hora_inicio AND data_hora_fim
    ");
    $stmt->execute([$profissional_id, $data_hora_inicio]);
    
    if ($stmt->fetch()) {
        $_SESSION['erro'] = "O profissional selecionado não está disponível neste horário";
        header('Location: agendamento.php');
        exit;
    }
    
    // Calcula a hora de término (assumindo 1 hora de duração)
    $data_hora_fim = date('Y-m-d H:i:s', strtotime($data_hora_inicio . ' +1 hour'));
    
    // Insere o agendamento
    $stmt = $pdo->prepare("
        INSERT INTO agendamentos 
        (cliente_id, profissional_id, servico_id, data_hora_inicio, data_hora_fim, status, criado_em) 
        VALUES (?, ?, ?, ?, ?, 'pendente', NOW())
    ");
    $stmt->execute([$cliente_id, $profissional_id, $servico_id, $data_hora_inicio, $data_hora_fim]);
    
    $_SESSION['sucesso'] = "Agendamento realizado com sucesso!";
    header('Location: home_auth.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro ao realizar agendamento: " . $e->getMessage();
    header('Location: agendamento.php');
    exit;
}