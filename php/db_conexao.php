<?php

    // Configurações do banco de dados PostgreSQL
    $host = 'localhost';  // Endereço do servidor
    $dbname = 'jogos';  // Nome do banco de dados
    $port = '5432'; // porta
    $username = 'postgres';  // Usuário do banco de dados
    $password = 'admin';  // Senha do banco de dados

    // Função para realizar a conexão com o banco de dados PostgreSQL
    function connectToDatabase() {
        global $host, $dbname, $port, $username, $password;
        try {
            // Criando a string de conexão
            $connectionString = "host=$host port=$port dbname=$dbname user=$username password=$password";
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
