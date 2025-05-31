<?php
    include_once 'db_conexao.php';

    function get_filtro($array_f){
        $filtro = [];
        foreach ($array_f as $af) if ($af != "") $filtro[] = $af;
        $filtro = implode(' AND ', $filtro);
        if($filtro != "") $filtro = " WHERE ".$filtro;
        return $filtro;
    }

    //Lista Statistica Jogadores
    function get_duplas_estatistica($order="partidas DESC"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_dupla_estatistica 
                ORDER BY $order";
        $r = executeQuery($sql);
        if ( $r["success"] && count($r["data"]) > 0 ) $resultado['get_duplas_estatistica'] = $r["data"];
    }

    //Lista Statistica Jogadores
    function get_jogadores_estatistica($id_jogador="NULL OR NULL IS NULL", $order="partidas desc"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica 
                WHERE id = $id_jogador 
                ORDER BY $order";
        $r = executeQuery($sql);
        if ( $r["success"] && count($r["data"]) > 0 ) $resultado['get_jogadores_estatistica'] = $r["data"];
    }

    //Lista jogadores
    function get_jogadores($id_jogador="NULL OR NULL IS NULL"){
        global $resultado;
        $sql = "SELECT *
                FROM jogador 
                WHERE id = $id_jogador 
                ORDER BY nome;";
        $r = executeQuery($sql);
        if ( $r["success"] && count($r["data"]) > 0 ) $resultado['get_jogadores'] = $r["data"];
    }

    //Lista partidas
    function get_partidas($id_partida="NULL OR NULL IS NULL"){
        global $resultado;
        $sql = "SELECT *
                FROM partida
                WHERE id = $id_partida
                ORDER BY id DESC, data_hora DESC;";
        $r = executeQuery($sql);
        if ( $r["success"] && count($r["data"]) > 0 ) $resultado['get_partidas'] = $r["data"];
    }

    // Verifica se o método de requisição é POST
    /*if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $opcao = isset($_POST['opcao']) ? $_POST['opcao'] : '';*/
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $opcao = isset($_GET['opcao']) ? $_GET['opcao'] : '';
        if($opcao == "ALL") $opcao = ['get_jogadores', 'get_partidas', 'get_jogadores_estatistica', 'get_duplas_estatistica'];
        if (!is_array($opcao) && strpos($opcao, ',') !== false) $opcao = explode(',', $opcao);
        elseif (!is_array($opcao)) $opcao = [$opcao];
        
        $resultado = [];
        foreach ($opcao as $opc) {
            switch ($opc) {
                case 'get_jogadores':
                    get_jogadores();
                    break;
                case 'get_partidas':
                    get_partidas();
                    break;
                case 'get_jogadores_estatistica':
                    get_jogadores_estatistica();
                    break;
                case 'get_duplas_estatistica':
                    get_duplas_estatistica();
                    break;
            }
        }

        if(count($resultado) > 0) echo json_encode(array('data' => $resultado));
        else echo json_encode(array('error' => 'Resultado Vazio'));
                
    } else echo json_encode(array('error' => 'Método de requisição inválido.'));

?>
