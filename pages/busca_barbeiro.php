<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$data = $_POST['data'] ?? '';
$hora = $_POST['hora'] ?? '';

if (empty($data) || empty($hora)) {
    echo json_encode(['profissionais' => [], 'horarios_indisponiveis' => []]);
    exit;
}

$data_hora_inicio = $data . ' ' . $hora . ':00';
$data_hora_fim = date('Y-m-d H:i:s', strtotime($data_hora_inicio . ' +1 hour'));

// Buscar TODOS os horários indisponíveis para esta data (não apenas do horário específico)
$stmt = $pdo->prepare("
    SELECT DISTINCT DATE_FORMAT(data_hora_inicio, '%H:%i') as hora_agendada 
    FROM agendamentos 
    WHERE DATE(data_hora_inicio) = ? 
    AND status != 'cancelado'
    ORDER BY hora_agendada
");
$stmt->execute([$data]);
$horarios_indisponiveis = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Buscar profissionais disponíveis
$stmt = $pdo->prepare("
    SELECT u.* 
    FROM usuarios u
    WHERE u.tipo = 'profissional'
    AND NOT EXISTS (
        SELECT 1 
        FROM indisponibilidades i
        WHERE i.profissional_id = u.id
        AND (
            (? BETWEEN i.data_hora_inicio AND i.data_hora_fim)
            OR (? BETWEEN i.data_hora_inicio AND i.data_hora_fim)
            OR (i.data_hora_inicio BETWEEN ? AND ?)
        )
    )
    AND NOT EXISTS (
        SELECT 1 
        FROM agendamentos a
        WHERE a.profissional_id = u.id
        AND (
            (? BETWEEN a.data_hora_inicio AND a.data_hora_fim)
            OR (? BETWEEN a.data_hora_inicio AND a.data_hora_fim)
            OR (a.data_hora_inicio BETWEEN ? AND ?)
        )
        AND a.status != 'cancelado'
    )
");
$stmt->execute([
    $data_hora_inicio, $data_hora_fim, $data_hora_inicio, $data_hora_fim,
    $data_hora_inicio, $data_hora_fim, $data_hora_inicio, $data_hora_fim
]);
$profissionais = $stmt->fetchAll();

echo json_encode([
    'profissionais' => $profissionais,
    'horarios_indisponiveis' => $horarios_indisponiveis
]);