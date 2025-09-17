<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require "../backend/conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit;
}

$id = $_SESSION["usuario_id"];

// Busca dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Data de hoje
$dataHoje = date('Y-m-d');

// Verifica se o usuário quer modificar o treino
if(isset($_POST['modificar_treino'])){
    // Apaga treino do dia atual
    $stmtDelete = $pdo->prepare("DELETE FROM treino_usuario WHERE usuario_id = :id AND data = :data");
    $stmtDelete->execute(['id' => $id, 'data' => $dataHoje]);

    // Faz com que o formulário apareça novamente
    $exerciciosHoje = [];
}

// Verifica se o usuário já escolheu treino hoje
$stmtCheck = $pdo->prepare("
    SELECT tt.nome_treino, tt.exercicio, tt.series, tt.repeticoes
    FROM treino_usuario tu
    JOIN treino_tipo tt ON tu.treino_tipo_id = tt.id
    WHERE tu.usuario_id = :id AND tu.data = :data
");
$stmtCheck->execute(['id' => $id, 'data' => $dataHoje]);
$exerciciosHoje = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel do Aluno</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="painel">
  <h1>Olá, <?php echo htmlspecialchars($usuario["nome"]); ?>!</h1>
  <img src="../uploads/<?php echo htmlspecialchars($usuario["foto"]); ?>" width="150" alt="Foto do usuário">

  <?php if(empty($exerciciosHoje)): ?>
    <h2>Qual treino você deseja escolher hoje?</h2>
    <form action="/projeto%20Academia/backend/escolher_treino.php" method="POST">
      <select name="treino" required>
        <option value="">Selecione...</option>
        <option value="Perna">Perna</option>
        <option value="Ombro + Trapézio">Ombro + Trapézio</option>
        <option value="Peito + Bíceps">Peito + Bíceps</option>
        <option value="Costas + Bíceps">Costas + Bíceps</option>
        <option value="Cardio + Core">Cardio + Core</option>
      </select>
      <button type="submit">Escolher treino</button>
    </form>
  <?php else: ?>
    <h2>Treino escolhido hoje: <?php echo htmlspecialchars($exerciciosHoje[0]['nome_treino']); ?></h2>
    <ul>
      <?php foreach($exerciciosHoje as $ex): ?>
        <li><?php echo htmlspecialchars("{$ex['exercicio']} - {$ex['series']}x{$ex['repeticoes']}"); ?></li>
      <?php endforeach; ?>
    </ul>
    <!-- Botão para modificar treino -->
    <form method="POST">
        <button type="submit" name="modificar_treino">Escolher outro treino</button>
    </form>
  <?php endif; ?>

  <a href="../backend/logout.php" class="sair">Sair</a>
</div>
</body>
</html>
