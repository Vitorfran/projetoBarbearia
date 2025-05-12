<?php
require_once _DIR_ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['perfil'] ?? 'cliente'; // Agora pode ser 'cliente' ou 'profissional'
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

            if ($success) {
                $usuarioId = $pdo->lastInsertId();

                // Se for profissional (barbeiro), associar todos os serviços ativos
                if ($tipo === 'profissional') {
                    $servicos = $pdo->query("SELECT id FROM servicos WHERE ativo = 1")->fetchAll(PDO::FETCH_COLUMN);

                    if (!empty($servicos)) {
                        $stmtInsert = $pdo->prepare("INSERT INTO profissionais_servicos (profissional_id, servico_id) VALUES (?, ?)");

                        foreach ($servicos as $servicoId) {
                            $stmtInsert->execute([$usuarioId, $servicoId]);
                        }
                    }
                }

                echo "Usuário cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar usuário.";
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/cadastro.css">
    <title>Cadastro | CortAí</title>
</head>
<body>
    <div class="cadastro-box">
        <div class="cadastro-header">
            <header>Cadastro</header>
        </div>
        <form action="cadastro.php" method="POST">
            <div class="input-box">
                <input type="text" class="input-field" placeholder="Nome" name="nome" autocomplete="off" required>
            </div>
            <div class="input-box">
                <input type="email" class="input-field" placeholder="Email" name="email" autocomplete="off" required>
            </div>
            <div class="input-box">
                <input type="password" class="input-field" placeholder="Senha" name="senha" autocomplete="off" required>
            </div>
            <div class="forgot">
                <section>
                    <input type="radio" id="cliente" name="perfil" value="cliente" class="animated-radio" checked>
                    <label for="cliente">Sou cliente</label>
                </section>
                <section>
                    <input type="radio" id="profissional" name="perfil" value="profissional" class="animated-radio">
                    <label for="profissional">Sou barbeiro</label>
                </section>              
            </div>
            <div class="input-submit">
                <button type="submit" class="submit-btn">Cadastrar</button>
            </div>
        </form>
        <div class="sign-up-link">
            <p>Já tem uma conta? <a href="../pages/login.php">Entrar</a></p>
        </div>
    </div>
</body>
</html>