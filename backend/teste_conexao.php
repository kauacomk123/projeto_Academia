<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=academia;charset=utf8", "root", "Santana27");
    echo "Conexão OK";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}