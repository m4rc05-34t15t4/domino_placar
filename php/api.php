<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

include_once 'db_conexao.php';
include_once 'sql.php';

$resultado = [];

function get_filtro($array_f){
    $filtro = [];
    foreach ($array_f as $af) if ($af != "") $filtro[] = $af;
    $filtro = implode(' AND ', $filtro);
    if($filtro != "") $filtro = " WHERE ".$filtro;
    return $filtro;
}

function nome_e($n){
    $e = '';
    switch ($n) {
        case 'dentro':
            $e = '_expediente';
            break;
        case 'fora':
            $e = '_fora_expediente';
            break;
    }
    return $e;
}

function nome_j($n){
    return $n == "sinuca" ? "_sinuca" : '';
}

// Lista rank mensal dominó
function get_rank_mensal($exp="", $jog="", $order="order by ano desc, mes desc"){
    global $resultado;
    $sql = $jog == "sinuca" ? gerarSqlRankingSinucaMensal($exp, $order) : gerarSqlRankingMensal($exp, $order);
    resultado_array(executeQuery($sql), 'get_rank'.nome_j($jog).'_mensal'.nome_e($exp));
}

// Lista jogadores
function get_jogadores($id_jogador="IS NOT NULL"){
    global $resultado;
    $sql = "SELECT *
            FROM jogador 
            WHERE id $id_jogador
            ORDER BY nome;";
    $r = executeQuery($sql);
    resultado_array($r, 'get_jogadores');
}

// Lista partidas
function get_partidas($id_partida="IS NOT NULL"){
    global $resultado;
    $sql = "SELECT *
            FROM partida
            WHERE id $id_partida
            ORDER BY id DESC, data_hora DESC LIMIT 150;";
    $r = executeQuery($sql);
    resultado_array($r, 'get_partidas');
}

// Lista Statistica Duplas Jogadores
function get_duplas_estatistica($exp = "", $order = "ORDER BY partidas DESC"){
    global $resultado;
    $sql = sqlDuplasEstatisticas($exp, $order);
    resultado_array(executeQuery($sql), 'get_duplas_estatistica'.nome_e($exp));
}

// Lista Statistica Jogadores
function get_jogadores_estatistica($exp="", $jog=""){
    global $resultado;
    $sql = $jog == "sinuca" ? gerarSqlJogadorEstatisticaSinuca($exp) : gerarSqlJogadorEstatistica($exp);
    $r = executeQuery($sql);
    resultado_array($r, 'get_jogadores_estatistica'.nome_j($jog).nome_e($exp));
}

// Lista Statistica Rivais Jogadores Sinuca
function get_rivais_estatistica_sinuca(){
    global $resultado;
    $sql = sqlRankingConfrontosSinuca();
    $r = executeQuery($sql);
    resultado_array($r, 'get_rivais_estatistica_sinuca');
}

// Lista partidas sinuca
function get_partidas_sinuca($id_partida="IS NOT NULL"){
    global $resultado;
    $sql = "SELECT *
            FROM partida_sinuca
            WHERE id $id_partida
            ORDER BY id DESC, data_hora DESC LIMIT 150;";
    $r = executeQuery($sql);
    resultado_array($r, 'get_partidas_sinuca');
}

function resultado_array($r, $k){
    global $resultado;
    if ( $r["success"] && is_countable($r["data"]) && count($r["data"]) > 0 ) $resultado[$k] = $r["data"];
}

// Verifica se o método de requisição é GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $opcao = isset($_GET['opcao']) ? $_GET['opcao'] : '';
    if($opcao == "all" OR $opcao == "ALL") $opcao = ['get_jogadores', 'get_partidas', 'get_jogadores_estatistica', 'get_duplas_estatistica', 'get_rank_mensal'];
    elseif($opcao == "SINUCA" OR $opcao == "sinuca") $opcao = ['get_jogadores', 'get_partidas_sinuca', 'get_jogadores_estatistica_sinuca', 'get_rivais_estatistica_sinuca', 'get_rank_sinuca_mensal'];
    if (!is_array($opcao) && strpos($opcao, ',') !== false) $opcao = explode(',', $opcao);
    elseif (!is_array($opcao)) $opcao = [$opcao];
    
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
                get_jogadores_estatistica("dentro");
                get_jogadores_estatistica("fora");
                break;
            case 'get_duplas_estatistica':
                get_duplas_estatistica();
                break;
            case 'get_rank_mensal':
                get_rank_mensal("");
                get_rank_mensal("dentro");
                get_rank_mensal("fora");
                break;
            case 'get_partidas_sinuca':
                get_partidas_sinuca();
                break;
            case 'get_jogadores_estatistica_sinuca':
                get_jogadores_estatistica('', 'sinuca');
                get_jogadores_estatistica('dentro', 'sinuca');
                get_jogadores_estatistica('fora', 'sinuca');
                break;
            case 'get_rank_sinuca_mensal':
                get_rank_mensal("", "sinuca");
                get_rank_mensal("dentro", "sinuca");
                get_rank_mensal("fora", "sinuca");
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
