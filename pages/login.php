<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Se já estiver logado, redireciona de acordo com o tipo
if (isset($_SESSION['usuario'])) {
    redirectBasedOnUserType();
}

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['erro'] = "Preencha todos os campos!";
        header('Location: login.php');
        exit;
    }

    // Busca o usuário no banco de dados (incluindo o campo 'admin')
    $stmt = $pdo->prepare("SELECT id, nome, senha, tipo, admin FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Verifica se o usuário existe e a senha está correta
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Autenticação bem-sucedida
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $email,
            'tipo' => $usuario['tipo'],
            'admin' => $usuario['admin'] // Adiciona o status de admin à sessão
        ];
        
        // Redireciona para a página apropriada
        redirectBasedOnUserType();
    } else {
        $_SESSION['erro'] = "E-mail ou senha incorretos!";
        header('Location: login.php');
        exit;
    }
}

// Função para redirecionar baseado no tipo de usuário
function redirectBasedOnUserType() {
    if ($_SESSION['usuario']['admin'] == 1) {
        // Se for admin, redireciona para a tela de admin
        header('Location: /projetoBarbearia/pages/tela_admin.php');
    } elseif ($_SESSION['usuario']['tipo'] === 'profissional') {
        header('Location: /projetoBarbearia/pages/telabarbeiro.php');
    } else {
        header('Location: /projetoBarbearia/pages/home_auth.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CortAí</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <!-- Adicione o Bootstrap CSS se estiver usando -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
        require_once __DIR__ . '/../partes/header.php'
    ?>
    <div class="cadastro-box">
        <div class="cadastro-header">
            <h1 class="titulo" >Login</h1>
        </div>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['erro']; unset($_SESSION['erro']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="input-box">
                <input type="email" name="email" class="input-field" placeholder="Email" required>
            </div>
            <div class="input-box">
                <input type="password" name="senha" class="input-field" placeholder="Senha" required>
            </div>
            <div class="forgot">
                <section>
                    <input type="checkbox" id="check" name="lembrar">
                    <label for="check">Lembrar usuário</label>
                </section>
                <section>
                    <a href="recuperar_senha.php">Esqueceu sua senha?</a>
                </section>
            </div>
            <div class="input-submit">
                <button type="submit" class="submit-btn">Entrar</button>
            </div>
        </form>
        
        <div class="sign-up-link">
            <p>Não tem uma conta? <a href="cadastro.php">Criar conta</a></p>
        </div>
    </div>
    
    <?php require_once '../partes/footer.php'; ?>
    
    <!-- Adicione o Bootstrap JS se estiver usando -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>