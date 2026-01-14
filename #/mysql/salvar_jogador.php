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
            $sql = "INSERT INTO jogador (nome) VALUES ('$nome');";
            break;
        case 'UPDATE':
            $sql = "UPDATE jogador SET nome = '$nome' WHERE id = $id_jogador;";
            break;
        case 'DELETE':
            $sql = "DELETE FROM jogador WHERE id = $id_jogador;";
            break;
        default:
            http_response_code(400);
            echo json_encode(['erro' => 'Ação inválida']);
            exit;
    }

    if($sql != "") {
        $resultado = executeQuery($sql);

        // Ajusta o resultado para mostrar o ID ou affected_rows
        if ($acao == 'INSERT' && $resultado['success']) {
            $resultado['insert_id'] = $resultado['insert_id'];
        } elseif (($acao == 'UPDATE' || $acao == 'DELETE') && $resultado['success']) {
            $resultado['affected_rows'] = $resultado['affected_rows'];
        }

        echo json_encode(['resultado' => $resultado]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
