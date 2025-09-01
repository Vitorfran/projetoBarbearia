<?php
session_start();
require_once '../config/database.php';

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Função para enviar uma resposta de erro e terminar o script
function send_error($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// Valida se os dados essenciais foram enviados
if (empty($_POST['servico_id']) || empty($_POST['data']) || empty($_POST['hora'])) {
    // Retorna um array vazio se os dados estiverem incompletos, pois não há o que buscar
    echo json_encode([]);
    exit;
}

$servico_id = $_POST['servico_id'];
$data = $_POST['data'];
$hora = $_POST['hora'];

try {
    // 1. Buscar a duração do serviço selecionado
    $stmtDuracao = $pdo->prepare("SELECT duracao FROM servicos WHERE id = ?");
    $stmtDuracao->execute([$servico_id]);
    $duracao = $stmtDuracao->fetchColumn();

    // Se o serviço não for encontrado ou não tiver duração, não há como continuar
    if (!$duracao) {
        echo json_encode([]);
        exit;
    }

    // 2. Calcular o período de tempo (início e fim) do agendamento desejado
    $inicio_desejado = new DateTime("$data $hora");
    $fim_desejado = (clone $inicio_desejado)->modify("+$duracao minutes");
    $inicio_sql = $inicio_desejado->format('Y-m-d H:i:s');
    $fim_sql = $fim_desejado->format('Y-m-d H:i:s');

    // 3. Consulta OTIMIZADA com LEFT JOIN
    // A ideia é:
    // a) Selecionar todos os profissionais (usuarios u).
    // b) Juntar (LEFT JOIN) os agendamentos (agendamentos a) que CONFLITAM com o horário desejado.
    // c) Filtrar (WHERE a.id IS NULL) para manter APENAS os profissionais que NÃO tiveram nenhum agendamento conflitante encontrado.
    $sql = "
        SELECT u.id, u.nome
        FROM usuarios u
        LEFT JOIN agendamentos a ON u.id = a.profissional_id
            AND a.status != 'cancelado'
            AND a.data_hora_inicio < :fim_desejado
            AND a.data_hora_fim > :inicio_desejado
        WHERE u.tipo = 'profissional'
          AND a.id IS NULL
        GROUP BY u.id, u.nome
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':inicio_desejado' => $inicio_sql,
        ':fim_desejado' => $fim_sql
    ]);

    $profissionais_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna a lista de profissionais disponíveis em formato JSON
    echo json_encode($profissionais_disponiveis);

} catch (Exception $e) {
    // Se ocorrer qualquer erro no processo, envie uma resposta de erro clara
    // Isso ajuda a depurar o problema no navegador (F12 > Network)
    send_error("Erro no servidor: " . $e->getMessage());
}