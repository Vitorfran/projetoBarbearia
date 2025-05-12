
=======
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['erro'] = "Preencha todos os campos!";
        header('Location: login.php');
        exit;
    }

    // Busca o usuário no banco de dados
    $stmt = $pdo->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Verifica se o usuário existe e a senha está correta
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Autenticação bem-sucedida
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $email,
            'tipo' => $usuario['tipo']
        ];
        
        // Redireciona para a página apropriada
        header('Location: ../public/home.php');
        exit;
    } else {
        $_SESSION['erro'] = "E-mail ou senha incorretos!";
        header('Location: login.php');
        exit;
    }
}
?>

>>>>>>> Stashed changes
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <title>Login | CortAí</title>
</head>
<body>
    <div class="cadastro-box">
        <div class="cadastro-header">
            <header>Login</header>
        </div>
        <div class="input-box">
            <input type="text" class="input-field" placeholder="Nome" autocomplete="off" required>
        </div>
        <div class="input-box">
            <input type="text" class="input-field" placeholder="Email" autocomplete="off" required>
        </div>
        <div class="input-box">
            <input type="password" class="input-field" placeholder="Senha" autocomplete="off" required>
        </div>
        <div class="forgot">
            <section>
                <input type="checkbox" id="check">
                <label for="check">Lembrar usuário</label>
            </section>
            <section>
                <a href="#">Esqueceu sua senha?</a>
            </section>
        </div>
        <div class="input-submit">
            <button class="submit-btn" id="submit"></button>
            <label for="submit">Entrar</label>
        </div>
        <div class="sign-up-link">
            <p>Não tem uma conta? <a href="../pages/cadastro.php">Criar conta</a></p>
        </div>
    </div>
</body>
</html>