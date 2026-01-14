<?php

include_once('db_conexao.php');

header('Content-Type: application/json');

try {

    // Lê os dados JSON da requisição
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados inválidos']);
        exit;
    }

    // Extrai os dados
    $acao = $data['acao'] ?? null;
    $id_jogador = $data['id'] ?? null;
    $nome = $data['nome'] ?? null;

    // Verifica se os dados obrigatórios estão presentes
    if (!$nome || !$acao) {
        http_response_code(422);
        echo json_encode(['erro' => 'Campos obrigatórios ausentes']);
        exit;
    }

    $sql = "";
    switch ($acao) {
        case 'INSERT':
            $sql = "INSERT INTO jogador (nome) VALUES ('$nome') RETURNING id;";
            break;
        case 'UPDATE':
            $sql = "UPDATE jogador SET nome = '$nome' WHERE id = $id_jogador RETURNING *;";
            break;
        case 'DELETE':
            $sql = "DELETE FROM jogador WHERE id = $id_jogador RETURNING id;";
            break;
    }

    if($sql != "") $resultado = executeQuery($sql);
    echo json_encode(['resultado' => $resultado]);
    

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
