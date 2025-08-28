<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$data = $_POST['data'] ?? '';

if (empty($data)) {
    echo json_encode([]);
    exit;
}

// Buscar horários indisponíveis para esta data
$stmt = $pdo->prepare("
    SELECT DISTINCT DATE_FORMAT(data_hora_inicio, '%H:%i') as hora_agendada 
    FROM agendamentos 
    WHERE DATE(data_hora_inicio) = ? 
    AND status != 'cancelado'
    ORDER BY hora_agendada
");
$stmt->execute([$data]);
$horarios_indisponiveis = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($horarios_indisponiveis);