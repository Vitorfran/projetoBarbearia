<?php

require_once __DIR__ . '/../config/database.php';

// PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $perfil = $_POST['perfil'] ?? 'cliente';
    $tipo = ($perfil === 'barbeiro') ? 'profissional' : 'cliente';
    $admin = false;

    if (empty($nome) || empty($email) || empty($senha)) {
        $_SESSION['erro'] = "Preencha todos os campos!";
        header("Location: cadastro.php");
        exit;
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $_SESSION['erro'] = "Este e-mail já está cadastrado.";
            header("Location: cadastro.php");
            exit;
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, admin) VALUES (?, ?, ?, ?, ?)");
            $success = $stmt->execute([$nome, $email, $senhaHash, $tipo, $admin]);

            if ($success) {
                // Busca o ID do usuário recém-criado
                $id = $pdo->lastInsertId();
                
                // Se o usuário for um barbeiro, associamos ele a todos os serviços disponíveis
                if ($tipo === 'profissional') {
                    // Obter todos os serviços disponíveis
                    $servicos = $pdo->query("SELECT id FROM servicos")->fetchAll();

                    // Inserir o relacionamento entre o barbeiro e todos os serviços
                    foreach ($servicos as $servico) {
                        $stmt = $pdo->prepare("INSERT INTO profissionais_servicos (profissional_id, servico_id) VALUES (?, ?)");
                        $stmt->execute([$id, $servico['id']]);
                    }
                }

                // Cria a sessão do usuário
                $_SESSION['usuario'] = [
                    'id' => $id,
                    'nome' => $nome,
                    'email' => $email,
                    'tipo' => $tipo,
                    'admin' => $admin
                ];

                // Redireciona para a home
                // REDIRECIONAMENTO BASEADO NO TIPO DE USUÁRIO
              if ($tipo === 'profissional') {
    header("Location: /projetoBarbearia/pages/telabarbeiro.php");
} else {
    header("Location: /projetoBarbearia/pages/home_auth.php");
}
                exit;
            } else {
                $_SESSION['erro'] = "Erro ao cadastrar usuário.";
                header("Location: cadastro.php");
                exit;
            }
        }
    }
}

// Exibe mensagens de erro se existirem
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['erro']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/cadastro.css">
    <title>Cadastro | CortAí</title>
</head>
<body>
     <?php
        require_once __DIR__ . '/../partes/header.php'
    ?>
    <div class="cadastro-box2">
        <div class="cadastro-header">
            <h1>Cadastro</h1>
        </div>
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
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
                    <input type="radio" id="barbeiro" name="perfil" value="barbeiro" class="animated-radio">
                    <label for="barbeiro">Sou barbeiro</label>
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

    <?php require_once '../partes/footer.php';?>

</body>
</html>