<?php
    include_once 'db_conexao.php';

    function get_filtro($array_f){
        $filtro = [];
        foreach ($array_f as $af) if ($af != "") $filtro[] = $af;
        $filtro = implode(' AND ', $filtro);
        if($filtro != "") $filtro = " WHERE ".$filtro;
        return $filtro;
    }

    //Lista rank mensal dominó
    function get_rank_mensal($exp="", $jog="", $order="ano desc, mes desc"){
        global $resultado;
        $sql = "SELECT * FROM public.vw_rank".$jog."_mensal$exp ORDER BY $order;";
        resultado_array(executeQuery($sql), 'get_rank'.$jog.'_mensal'.$exp);
    }

    //Lista rank semanal domino
    function get_rank_semanal(){
        global $resultado;
        $sql = "SELECT * FROM public.vw_rank_semanal;";
        resultado_array(executeQuery($sql), 'get_rank_semanal');
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
                ORDER BY id DESC, data_hora DESC LIMIT 150;";
        $r = executeQuery($sql);
        resultado_array($r, 'get_partidas');
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

    //Lista Statistica Jogadores
    function get_jogadores_estatistica($exp="", $id_jogador="NULL OR NULL IS NULL", $order="partidas desc", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica$exp$ult 
                WHERE id = $id_jogador AND PARTIDAS > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica'.$exp);
    }

    //Lista Statistica Jogadores sinuca
    function get_jogadores_estatistica_sinuca($exp="", $id_jogador="NULL OR NULL IS NULL", $order="qtd_partidas_utilizadas desc, vitorias DESC", $ult="_ultimos_jogos"){
        global $resultado;
        $sql = "SELECT *
                FROM vw_jogador_estatistica_sinuca$exp$ult 
                WHERE id = $id_jogador AND qtd_partidas_utilizadas > 0 
                ORDER BY $order";
        $r = executeQuery($sql);
        resultado_array($r, 'get_jogadores_estatistica_sinuca'.$exp);
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

    //Lista partidas sinuca
    function get_partidas_sinuca($id_partida="NULL OR NULL IS NULL"){
        global $resultado;
        $sql = "SELECT *
                FROM partida_sinuca
                WHERE id = $id_partida
                ORDER BY id DESC, data_hora DESC LIMIT 150;";
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
        if($opcao == "all" OR $opcao == "ALL") $opcao = ['get_jogadores', 'get_partidas', 'get_jogadores_estatistica', 'get_duplas_estatistica', 'get_rank_mensal'];
        elseif($opcao == "SINUCA" OR $opcao == "sinuca") $opcao = ['get_jogadores', 'get_partidas_sinuca', 'get_jogadores_estatistica_sinuca', 'get_rivais_estatistica_sinuca', 'get_rank_sinuca_mensal'];
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
                    get_jogadores_estatistica("_expediente");
                    get_jogadores_estatistica("_fora_expediente");
                    get_jogadores_estatistica($exp="_rank", $id_jogador="NULL OR NULL IS NULL", $order="vitorias DESC", $ult="");
                    break;
                case 'get_duplas_estatistica':
                    get_duplas_estatistica();
                    break;
                case 'get_rank_semanal':
                    get_rank_semanal();
                    break;
                case 'get_rank_mensal':
                    get_rank_mensal("");
                    get_rank_mensal("_expediente");
                    get_rank_mensal("_fora_expediente");
                    break;
                case 'get_partidas_sinuca':
                    get_partidas_sinuca();
                    break;
                case 'get_jogadores_estatistica_sinuca':
                    get_jogadores_estatistica_sinuca();
                    get_jogadores_estatistica_sinuca('_expediente');
                    get_jogadores_estatistica_sinuca('_fora_expediente');
                    get_jogadores_estatistica_sinuca($exp="_rank", $id_jogador="NULL OR NULL IS NULL", $order="vitorias DESC", $ult="");
                    break;
                case 'get_rank_sinuca_mensal':
                    get_rank_mensal("", "_sinuca");
                    get_rank_mensal("_expediente", "_sinuca");
                    get_rank_mensal("_fora_expediente", "_sinuca");
                    break;
                case 'get_rivais_estatistica_sinuca':
                    get_rivais_estatistica_sinuca();
                    break;
            }
        }

        if(count($resultado) > 0) echo json_encode(array('data' => $resultado));
        else echo json_encode(array('error' => 'Resultado Vazio'));
                
    } else echo json_encode(array('error' => 'Método de requisição inválido.'));

?>
