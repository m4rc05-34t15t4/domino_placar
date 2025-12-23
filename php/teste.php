<?php
//neon
$host = "ep-dark-brook-acw1joyq.sa-east-1.aws.neon.tech";
$dbname = "jogos";
$user = "neondb_owner";
$pass = "npg_5qFZjTsRgy4t"; // gere outra!
$endpoint = "ep-dark-brook-acw1joyq";
$conn_string = "
host=$host
dbname=$dbname
user=$user
password=$pass
sslmode=require
options=endpoint=$endpoint
";

//supabase
/*$host = "db.xjxabtlciyxwmwnbnebp.supabase.co";
$port = "5432";
$dbname = "postgres";
$username = "postgres";
$password = "jogos_postgres_supabase";
$connectionString = "host=$host port=$port dbname=$dbname user=$username password=$password sslmode=require";*/

$db = pg_connect($connectionString);

if (!$db) {
    die("Erro na conexão com PostgreSQL");
}

// 🔹 Teste
$result = pg_query($db, "SELECT * FROM jogador ORDER BY nome;");

$data = pg_fetch_all($result);

print_r($data);

while ($row = pg_fetch_assoc($result)) {
    echo $row['nome'] . "<br>";
}
