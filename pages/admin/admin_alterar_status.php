<?php
session_start();
require_once '../../config/database.php'; // Ajuste o caminho conforme necessário

// Ativar exibição de erros para depuração (REMOVA EM PRODUÇÃO!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- AUTHENTICATION AND AUTHORIZATION ---
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para acessar esta página.";
    header('Location: ../login.php'); // Ajuste o caminho se necessário
    exit;
}
// --- END AUTHENTICATION ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agendamento_id = $_POST['id'] ?? null;
    $acao = $_POST['acao'] ?? null; // 'cancelar', 'confirmar', 'concluir'

    if (empty($agendamento_id) || empty($acao)) {
        $_SESSION['mensagem'] = "Erro: ID do agendamento ou ação não fornecidos.";
        header('Location: ../tela_admin.php#agendamentos-section');
        exit;
    }

    $novo_status = '';
    switch ($acao) {
        case 'cancelar':
            $novo_status = 'cancelado';
            break;
        case 'confirmar':
            $novo_status = 'confirmado';
            break;
        case 'concluir':
            $novo_status = 'concluído';
            break;
        default:
            $_SESSION['mensagem'] = "Erro: Ação inválida.";
            header('Location: ../tela_admin.php#agendamentos-section');
            exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE agendamentos SET status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $agendamento_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['mensagem'] = "Agendamento " . $novo_status . " com sucesso!";
        } else {
            $_SESSION['mensagem'] = "Erro: Agendamento não encontrado ou status já era " . $novo_status . ".";
        }

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "Erro ao alterar status do agendamento: " . $e->getMessage();
    }

    header('Location: ../tela_admin.php#agendamentos-section');
    exit;

} else {
    // Se a requisição não for POST, redireciona de volta
    $_SESSION['mensagem'] = "Acesso inválido à página de alteração de status.";
    header('Location: ../tela_admin.php#agendamentos-section');
    exit;
}
?>