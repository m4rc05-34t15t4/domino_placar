<?php

include_once('db_conexao.php');

header('Content-Type: application/json');

try {

    date_default_timezone_set('America/Sao_Paulo');
    $dataHora = date('Y-m-d\TH:i');

    // Lê os dados JSON da requisição
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados inválidos']);
        exit;
    }

    // Extrai os dados
    $acao = $data['acao'] ?? null;
    $id_partida = $data['id'] ?? null;
    //$dataHora = $data['dataHora'] ?? null;
    $jogadorbct = $data['jogadorbct'] ?? null;
    $jogador1 = $data['jogador1'] ?? null;
    $jogador2 = $data['jogador2'] ?? null;
    $placar1 = $data['placar1'] ?? null;
    $placar2 = $data['placar2'] ?? null;

    // Verifica se os dados obrigatórios estão presentes
    if (!$acao || !$jogador1 || !$jogador2) {
        http_response_code(422);
        echo json_encode(['erro' => 'Campos obrigatórios ausentes']);
        exit;
    }

    if ($jogadorbct == 0) $jogadorbct = "NULL";

    $sql = "";
    switch ($acao) {
        case 'INSERT':
            $sql = "INSERT INTO partida_sinuca ( data_hora, jogador1_id, jogador2_id, placar1, placar2, jogadorbct ) 
                VALUES ( '$dataHora', $jogador1, $jogador2, $placar1, $placar2, $jogadorbct ) RETURNING ID;";
            break;
        case 'UPDATE':
            $sql = "UPDATE partida_sinuca SET 
                jogador1_id = $jogador1,
                jogador2_id = $jogador2,
                placar1 = $placar1,
                placar2 = $placar2,
                jogadorbct = $jogadorbct 
                WHERE id = $id_partida RETURNING *;";
            break;
        case 'DELETE':
            $sql = "DELETE FROM partida_sinuca WHERE id = $id_partida RETURNING ID;";
            break;
    }
    
    if($sql != "") $resultado = executeQuery($sql);
    echo json_encode(['resultado' => $resultado]);
    

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
