<?php
require "conexao.php"; // inclui $pdo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $data_nascimento = $_POST["data_nascimento"];
    $telefone = $_POST["telefone"];
    $endereco = $_POST["endereco"];

    // Upload da foto
    $foto = $_FILES["foto"]["name"];
    $destino = "../uploads/" . basename($foto);
    move_uploaded_file($_FILES["foto"]["tmp_name"], $destino);

    try {
        // Cadastrar usuário
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, data_nascimento, telefone, endereco, foto) 
            VALUES (:nome, :email, :senha, :data_nascimento, :telefone, :endereco, :foto)");
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha' => $senha,
            'data_nascimento' => $data_nascimento,
            'telefone' => $telefone,
            'endereco' => $endereco,
            'foto' => $foto
        ]);

        $usuario_id = $pdo->lastInsertId();

        // Treinos fixos
        $treinos = [
            "segunda" => "Peito + Tríceps",
            "terca" => "Costas + Bíceps",
            "quarta" => "Pernas",
            "quinta" => "Ombros + Trapézio",
            "sexta" => "Cardio + Core"
        ];

        foreach ($treinos as $dia => $treino) {
            $stmtTreino = $pdo->prepare("INSERT INTO treinos (usuario_id, dia_semana, treino) VALUES (:usuario_id, :dia, :treino)");
            $stmtTreino->execute([
                'usuario_id' => $usuario_id,
                'dia' => $dia,
                'treino' => $treino
            ]);
        }

        header("Location: ../frontend/login.html");
        exit;

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
