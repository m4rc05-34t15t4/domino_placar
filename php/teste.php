<?php

// Neon sao paulo MySQL
$host = "localhost";      // coloque o host MySQL
$dbname = "jogos";
$username = "root";   // usuário MySQL
$password = "root";     // senha MySQL
$port = "3306";                // porta padrão MySQL

if(is_localhost()){
    $host = 'localhost';  
    $dbname = 'jogos';
    $port = '3306'; 
    $username = 'root';  
    $password = 'root';  
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


$sql = "SELECT * FROM jogador ORDER BY nome;";

var_dump(executeQuery($sql));

?>




