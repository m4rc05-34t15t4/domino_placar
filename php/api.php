<?php
    include_once 'db_conexao.php';

    function get_filtro($array_f){
        $filtro = [];
        foreach ($array_f as $af) if ($af != "") $filtro[] = $af;
        $filtro = implode(' AND ', $filtro);
        if($filtro != "") $filtro = " WHERE ".$filtro;
        return $filtro;
    }

    //Lista Statistica Duplas Jogadores
    function get_rank_semanal(){
        global $resultado;
        $sql = "SELECT * FROM public.vw_rank_semanal;";
        resultado_array(executeQuery($sql), 'get_rank_semanal');
    }

    //Lista Statistica Duplas Jogadores
    function get_duplas_estatistica($order="partidas DESC"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_dupla_estatistica 
                WHERE PARTIDAS > 0 
                ORDER BY $order";
        resultado_array(executeQuery($sql), 'get_duplas_estatistica');
    }

    //Lista Statistica Rivais Jogadores Sinuca
    function get_rivais_estatistica_sinuca($order="partidas DESC"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_comparacao_rivais_sinuca 
                WHERE PARTIDAS > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_rivais_estatistica_sinuca');
    }

    //Lista Statistica Jogadores
    function get_jogadores_estatistica($id_jogador="NULL OR NULL IS NULL", $order="partidas desc", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica$ult 
                WHERE id = $id_jogador AND PARTIDAS > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica');
    }
    
    //com expediente
    function get_jogadores_estatistica_expediente($id_jogador="NULL OR NULL IS NULL", $order="partidas desc", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica_expediente$ult 
                WHERE id = $id_jogador AND PARTIDAS > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica_expediente');
    }

    //fora expediente
    function get_jogadores_estatistica_fora_expediente($id_jogador="NULL OR NULL IS NULL", $order="partidas desc", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica_fora_expediente$ult 
                WHERE id = $id_jogador AND PARTIDAS > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica_fora_expediente');
    }

    //Lista Statistica Jogadores sinuca
    function get_jogadores_estatistica_sinuca($id_jogador="NULL OR NULL IS NULL", $order="partidas_sinuca desc", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica_sinuca$ult 
                WHERE id = $id_jogador AND PARTIDAS_SINUCA > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica_sinuca');
    }

    //Lista Statistica Jogadores sinuca expediente de jogo
    function get_jogadores_estatistica_sinuca_expediente($id_jogador="NULL OR NULL IS NULL", $order="partidas_sinuca desc", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica_sinuca_expediente$ult 
                WHERE id = $id_jogador AND PARTIDAS_SINUCA > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica_sinuca_expediente');
    }

    //Lista Statistica Jogadores sinuca fora expediente
    function get_jogadores_estatistica_sinuca_fora_expediente($id_jogador="NULL OR NULL IS NULL", $order="partidas_sinuca desc", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica_sinuca_fora_expediente$ult 
                WHERE id = $id_jogador AND PARTIDAS_SINUCA > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica_sinuca_fora_expediente');
    }

    //Lista jogadores
    function get_jogadores($id_jogador="NULL OR NULL IS NULL"){
        global $resultado;
        $sql = "SELECT *
                FROM jogador 
                WHERE id = $id_jogador 
                ORDER BY nome;";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores');
    }

    //Lista partidas
    function get_partidas($id_partida="NULL OR NULL IS NULL"){
        global $resultado;
        $sql = "SELECT *
                FROM partida
                WHERE id = $id_partida
                ORDER BY id DESC, data_hora DESC;";
        $r = executeQuery($sql);
        resultado_array($r, 'get_partidas');
    }

    //Lista partidas sinuca
    function get_partidas_sinuca($id_partida="NULL OR NULL IS NULL"){
        global $resultado;
        $sql = "SELECT *
                FROM partida_sinuca
                WHERE id = $id_partida
                ORDER BY id DESC, data_hora DESC;";
        $r = executeQuery($sql);
        resultado_array($r, 'get_partidas_sinuca');
    }

    function resultado_array($r, $k){
        global $resultado;
        if ( $r["success"] && is_countable($r["data"]) && count($r["data"]) > 0 ) $resultado[$k] = $r["data"];
    }

    // Verifica se o método de requisição é POST
    /*if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $opcao = isset($_POST['opcao']) ? $_POST['opcao'] : '';*/
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $opcao = isset($_GET['opcao']) ? $_GET['opcao'] : '';
        if($opcao == "ALL") $opcao = ['get_jogadores', 'get_partidas', 'get_jogadores_estatistica', 'get_duplas_estatistica', 'get_rank_semanal', 'get_jogadores_estatistica_fora_expediente', 'get_jogadores_estatistica_expediente'];
        elseif($opcao == "SINUCA") $opcao = ['get_jogadores', 'get_partidas_sinuca', 'get_jogadores_estatistica_sinuca', 'get_rivais_estatistica_sinuca', 'get_jogadores_estatistica_sinuca_expediente', 'get_jogadores_estatistica_sinuca_fora_expediente'];
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
                case 'get_jogadores_estatistica_expediente':
                    get_jogadores_estatistica_expediente();
                    break;
                case 'get_jogadores_estatistica_fora_expediente':
                    get_jogadores_estatistica_fora_expediente();
                    break;
                case 'get_duplas_estatistica':
                    get_duplas_estatistica();
                    break;
                case 'get_partidas_sinuca':
                    get_partidas_sinuca();
                    break;
                case 'get_jogadores_estatistica_sinuca':
                    get_jogadores_estatistica_sinuca();
                    break;
                case 'get_jogadores_estatistica_sinuca_expediente':
                    get_jogadores_estatistica_sinuca_expediente();
                    break;
                case 'get_jogadores_estatistica_sinuca_fora_expediente':
                    get_jogadores_estatistica_sinuca_fora_expediente();
                    break;
                case 'get_rivais_estatistica_sinuca':
                    get_rivais_estatistica_sinuca();
                    break;
                case 'get_rank_semanal':
                    get_rank_semanal();
                    break;
            }
        }

        if(count($resultado) > 0) echo json_encode(array('data' => $resultado));
        else echo json_encode(array('error' => 'Resultado Vazio'));
                
    } else echo json_encode(array('error' => 'Método de requisição inválido.'));

?>
