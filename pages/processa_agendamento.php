<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro'] = 'Método não permitido';
    header('Location: agendamento.php');
    exit;
}

// Verificar se usuário está logado
if (!isset($_SESSION['usuario'])) {
    $_SESSION['erro'] = 'Você precisa estar logado para agendar';
    header('Location: ../public/home.php');
    exit;
}

// Validar dados do formulário
$servico_id = $_POST['servico_id'] ?? null;
$barbeiro_id = $_POST['barbeiro_id'] ?? null;
$data = $_POST['data'] ?? '';
$hora = $_POST['hora'] ?? '';

if (!$servico_id || !$barbeiro_id || !$data || !$hora) {
    $_SESSION['erro'] = 'Todos os campos são obrigatórios';
    header('Location: agendamento.php');
    exit;
}

// Validar formato da data e hora
$data_hora_inicio = $data . ' ' . $hora . ':00';
$data_hora_fim = date('Y-m-d H:i:s', strtotime($data_hora_inicio . ' +1 hour'));

if (!DateTime::createFromFormat('Y-m-d H:i:s', $data_hora_inicio)) {
    $_SESSION['erro'] = 'Data ou hora inválida';
    header('Location: agendamento.php');
    exit;
}

// Verificar se o horário já está agendado para este profissional
$stmt = $pdo->prepare("
    SELECT id 
    FROM agendamentos 
    WHERE profissional_id = ? 
    AND (
        (? BETWEEN data_hora_inicio AND data_hora_fim)
        OR (? BETWEEN data_hora_inicio AND data_hora_fim)
        OR (data_hora_inicio BETWEEN ? AND ?)
    )
    AND status != 'cancelado'
");
$stmt->execute([
    $barbeiro_id, 
    $data_hora_inicio, 
    $data_hora_fim,
    $data_hora_inicio,
    $data_hora_fim
]);
$agendamento_existente = $stmt->fetch();

if ($agendamento_existente) {
    $_SESSION['erro'] = 'Este horário já está agendado para o profissional selecionado';
    header('Location: agendamento.php');
    exit;
}

// Verificar se o profissional está indisponível neste horário
$stmt = $pdo->prepare("
    SELECT id 
    FROM indisponibilidades 
    WHERE profissional_id = ? 
    AND (
        (? BETWEEN data_hora_inicio AND data_hora_fim)
        OR (? BETWEEN data_hora_inicio AND data_hora_fim)
        OR (data_hora_inicio BETWEEN ? AND ?)
    )
");
$stmt->execute([
    $barbeiro_id, 
    $data_hora_inicio, 
    $data_hora_fim,
    $data_hora_inicio,
    $data_hora_fim
]);
$indisponibilidade = $stmt->fetch();

if ($indisponibilidade) {
    $_SESSION['erro'] = 'O profissional selecionado não está disponível neste horário';
    header('Location: agendamento.php');
    exit;
}

// Inserir agendamento no banco de dados
try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        INSERT INTO agendamentos 
        (cliente_id, profissional_id, servico_id, data_hora_inicio, data_hora_fim, status, criado_em) 
        VALUES (?, ?, ?, ?, ?, 'confirmado', NOW())
    ");
    $stmt->execute([
        $_SESSION['usuario']['id'],
        $barbeiro_id,
        $servico_id,
        $data_hora_inicio,
        $data_hora_fim
    ]);
    
    $pdo->commit();
    
    $_SESSION['sucesso'] = 'Agendamento realizado com sucesso!';
    header('Location: agendamento.php');
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erro'] = 'Erro ao processar agendamento: ' . $e->getMessage();
    header('Location: agendamento.php');
    exit;
}