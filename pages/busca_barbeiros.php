<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';
    
    if (empty($data) || empty($hora)) {
        throw new Exception("Data e hora são obrigatórias");
    }
    
    $data_hora = $data . ' ' . $hora . ':00';
    
    $stmt = $pdo->prepare("
        SELECT u.id, u.nome 
        FROM usuarios u
        WHERE u.tipo = 'profissional'
        AND NOT EXISTS (
            SELECT 1 
            FROM indisponibilidades i
            WHERE i.profissional_id = u.id
            AND ? BETWEEN i.data_hora_inicio AND i.data_hora_fim
        )
    ");
    $stmt->execute([$data_hora]);
    $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($profissionais);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}