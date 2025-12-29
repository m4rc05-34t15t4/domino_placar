<?php

    //Neon sao paulo
    $host = "ep-dark-brook-acw1joyq.sa-east-1.aws.neon.tech";
    $dbname = "jogos";
    $username = "neondb_owner";
    $password = "npg_5qFZjTsRgy4t";
    $endpoint = "ep-dark-brook-acw1joyq";
    $connectionString = " host=$host dbname=$dbname user=$username password=$password sslmode=require options=endpoint=$endpoint";

    if(is_localhost()){
        $host = 'localhost';  // Endereço do servidor
        $dbname = 'jogos';  // Nome do banco de dados
        $port = '5433'; // porta
        $username = 'postgres';  // Usuário do banco de dados
        $password = 'postgres';  // Senha do banco de dados
        $connectionString = "host=$host port=$port dbname=$dbname user=$username password=$password";
    }

    // Função para realizar a conexão com o banco de dados PostgreSQL
    function connectToDatabase() {
        global $connectionString;
        try {
            // Estabelecendo a conexão
            $connection = pg_connect($connectionString);
            if (!$connection) {
                // Se falhar na conexão, exibe a mensagem de erro
                die("Erro de conexão com o banco de dados.");
            }
            // Retorna a conexão
            return $connection;
        } catch (PDOException $e) {
            // Caso ocorra erro na conexão, exibe a mensagem de erro
            echo "Erro de conexão: " . $e->getMessage();
            exit();
        }
    }

    function is_localhost(){
        return explode(":", $_SERVER['HTTP_HOST'])[0] == 'localhost';
    }

    // Função para executar a consulta SQL e retornar o resultado
    function executeQuery($sql) {
        try {
            $connection = connectToDatabase();
            // Executa a consulta SQL
            $result = pg_query($connection, $sql);
            // Verifica se a consulta foi bem-sucedida
            if (!$result) {
                // Se falhar na execução da consulta, retorna um erro
                return ["success" => false, "error" => pg_last_error($connection)];
            }
            // Retorna os resultados em formato de array associativo
            $data = pg_fetch_all($result);
            //$data = pg_fetch_assoc($result);
            // Fecha a conexão após a consulta
            pg_close($connection);
            // Retorna os dados
            return ["success" => true, "data" => $data];
        } catch (PDOException $e) {
            // Caso ocorra erro na execução da consulta
            echo "Erro na consulta SQL: " . $e->getMessage();
            return null;  // Retorna null em caso de erro
        }
    }

?>
