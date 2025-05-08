<?php
// CONEXÃO COM BANCO (PDO)
require_once __DIR__ . '/../config/database.php';

// PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo = 'cliente';
    $admin = false;

    if (empty($nome) || empty($email) || empty($senha)) {
        echo "Preencha todos os campos!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo "Este e-mail já está cadastrado.";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, admin) VALUES (?, ?, ?, ?, ?)");
            $success = $stmt->execute([$nome, $email, $senhaHash, $tipo, $admin]);

            echo $success ? "Usuário cadastrado com sucesso!" : "Erro ao cadastrar usuário.";
        }
    }
}
?>
<!-- formulario de teste -->


<!-- <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
</head>
<body>
    <h2>Cadastro de Usuário</h2>
    <form method="POST" action="">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html> -->