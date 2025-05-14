<?php
session_start();
require_once '../config/database.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../public/home.php');
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_SESSION['usuario']['id']; // Pega o ID do cliente da sessão
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $servico_id = $_POST['servico_id'];
    $barbeiro_id = $_POST['barbeiro_id'];

    // Combina data e hora para o horário de início do agendamento
    $data_hora_inicio = $data . ' ' . $hora;

    // Calcula a hora de fim (exemplo: 1 hora depois)
    $data_hora_fim = date('Y-m-d H:i:s', strtotime($data_hora_inicio . ' +1 hour'));

    try {
        $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, profissional_id, servico_id, data_hora_inicio, data_hora_fim, status, criado_em)
                             VALUES (?, ?, ?, ?, ?, 'pendente', NOW())");

        $success = $stmt->execute([$cliente_id, $barbeiro_id, $servico_id, $data_hora_inicio, $data_hora_fim]);

        if ($success) {
            $_SESSION['sucesso'] = "Agendamento realizado com sucesso!";
            header('Location: home_auth.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao realizar o agendamento: " . $e->getMessage();
        header('Location: agendamento.php');
        exit;
    }
} else {
    header('Location: agendamento.php');
    exit;
}
?>