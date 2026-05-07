<?php

$dbHost = 'localhost';
$dbName = 'agencia_viagens';
$dbUser = 'seu_usuario';
$dbPass = 'sua_senha';
$dbCharset = 'utf8mb4';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

function getConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        global $dsn, $dbUser, $dbPass, $pdoOptions;
        $pdo = new PDO($dsn, $dbUser, $dbPass, $pdoOptions);
    }

    return $pdo;
}
