<?php

include_once('db_conexao.php');

header('Content-Type: application/json');

try {

    date_default_timezone_set('America/Sao_Paulo');
    $dataHora = date('Y-m-d H:i:s');

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
    $jogadas = $data['jogadas'] ?? null;
    $jogadorbct = $data['jogadorbct'] ?? null;
    $jogador1 = $data['jogador1'] ?? null;
    $jogador2 = $data['jogador2'] ?? null;
    $jogador3 = $data['jogador3'] ?? null;
    $jogador4 = $data['jogador4'] ?? null;
    $placar1 = $data['placar1'] ?? null;
    $placar2 = $data['placar2'] ?? null;

    // Verifica se os dados obrigatórios estão presentes
    if (!$acao || !$jogador1 || !$jogador2 || !$jogador3 || !$jogador4) {
        http_response_code(422);
        echo json_encode(['erro' => 'Campos obrigatórios ausentes']);
        exit;
    }

    // Ajustes para MySQL
    $jogadorbct = ($jogadorbct == 0) ? "NULL" : $jogadorbct;
    $jogadas = ($jogadas == "") ? "NULL" : "'".$jogadas."'";

    $sql = "";
    switch ($acao) {
        case 'INSERT':
            $sql = "INSERT INTO partida (
                        data_hora, jogador1_id, jogador2_id, jogador3_id, jogador4_id,
                        placar1, placar2, jogadorbct, jogadas
                    ) VALUES (
                        '$dataHora', $jogador1, $jogador2, $jogador3, $jogador4,
                        $placar1, $placar2, $jogadorbct, $jogadas
                    );";
            break;
        case 'UPDATE':
            $sql = "UPDATE partida SET 
                        jogador1_id = $jogador1,
                        jogador2_id = $jogador2,
                        jogador3_id = $jogador3,
                        jogador4_id = $jogador4,
                        placar1 = $placar1,
                        placar2 = $placar2,
                        jogadorbct = $jogadorbct,
                        jogadas = $jogadas
                    WHERE id = $id_partida;";
            break;
        case 'DELETE':
            $sql = "DELETE FROM partida WHERE id = $id_partida;";
            break;
        default:
            http_response_code(400);
            echo json_encode(['erro' => 'Ação inválida']);
            exit;
    }

    if($sql != "") {
        $resultado = executeQuery($sql);

        // Ajusta resultado para MySQL
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
