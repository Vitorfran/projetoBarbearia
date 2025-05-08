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