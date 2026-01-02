<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$host = "sql104.infinityfree.com"; // coloque o host MySQL
$dbname = "if0_40728404_jogos";
$username = "if0_40728404"; // usuário MySQL
$password = "mb11036095"; // senha MySQL
$port = "3306"; // porta padrão MySQL
$PREFIXO_DB = 'if0_40728404_';   

if(is_localhost()){
    $host = 'localhost';  
    $dbname = 'jogos';
    $port = '3306'; 
    $username = 'root';  
    $password = 'root';
    $GLOBALS['PREFIXO_DB'] = '';
}

// Função para verificar se está em localhost
function is_localhost(){
    return explode(":", $_SERVER['HTTP_HOST'])[0] == 'localhost';
}

// Função para conectar ao banco de dados MySQL
function connectToDatabase() {
    global $host, $dbname, $username, $password, $port;

    $connection = new mysqli($host, $username, $password, $dbname, $port);

    if ($connection->connect_error) {
        die("Erro de conexão com o banco de dados: " . $connection->connect_error);
    }

    // Define charset UTF8
    $connection->set_charset("utf8mb4");

    return $connection;
}

// Função para executar a consulta SQL e retornar o resultado
function executeQuery($sql) {
    try {
        $connection = connectToDatabase();
        $result = $connection->query($sql);

        if (!$result) {
            return ["success" => false, "error" => $connection->error];
        }

        $data = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        }

        $connection->close();

        return ["success" => true, "data" => $data];

    } catch (Exception $e) {
        echo "Erro na consulta SQL: " . $e->getMessage();
        return null;
    }
}

function renderTabela(array $dados): void {
    if (empty($dados)) {
        echo "<p><strong>Nenhum registro encontrado.</strong></p>";
        return;
    }
    echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;width:100%'>";
    // cabeçalho
    echo "<thead><tr>";
    foreach (array_keys($dados[0]) as $coluna) {
        echo "<th style='background:#eee;text-align:left'>" . htmlspecialchars($coluna) . "</th>";
    }
    echo "</tr></thead>";
    // corpo
    echo "<tbody>";
    foreach ($dados as $linha) {
        echo "<tr>";
        foreach ($linha as $valor) {
            echo "<td>" . htmlspecialchars((string)$valor) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

require_once ('sql.php');

$sql = gerarSqlJogadorEstatisticaSinuca('dentro');

echo $sql;

renderTabela(executeQuery($sql)['data']);

?>




