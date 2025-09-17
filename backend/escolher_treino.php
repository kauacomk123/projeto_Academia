<?php
session_start();
require "conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$treino = $_POST['treino'];
$data = date('Y-m-d');

// Salva a escolha do usuário
$stmt = $pdo->prepare("
    INSERT INTO treino_usuario (usuario_id, treino_tipo_id, data)
    SELECT :usuario_id, id, :data FROM treino_tipo WHERE nome_treino = :treino
");
$stmt->execute([
    'usuario_id' => $usuario_id,
    'treino'     => $treino,
    'data'       => $data
]);

header("Location: aluno.php");
exit;
