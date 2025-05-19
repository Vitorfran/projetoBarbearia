<?php
ob_start(); // Inicia o buffer de saída
require_once '../config/database.php';
require_once '../partes/header.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../public/home_auth.php');
    exit;
}

// Obtém os dados do usuário
$usuario_id = $_SESSION['usuario']['id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

// Processa atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    try {
        // Verifica se quer atualizar a senha
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $senha_hash, $usuario_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $usuario_id]);
        }

        // Atualiza os dados na sessão
        $_SESSION['usuario']['nome'] = $nome;
        $_SESSION['usuario']['email'] = $email;

        $_SESSION['mensagem'] = "Dados atualizados com sucesso!";
        header('Location: minha_conta.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao atualizar dados: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - Cortai</title>
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styleTelaBarbeiro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ESTILOS DO HEADER - COPIADOS DE MEUS_AGENDAMENTOS.PHP */
        body > header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 80px;
            background-color: #FFB22C;
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        body > header .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        body > header .logo img {
            height: 40px;
            width: auto;
        }

        body > header nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
            margin: 0;
            padding: 0;
        }

        body > header nav ul li {
            margin: 0;
        }

        body > header nav ul li a {
            text-decoration: none;
            color: #333;
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s;
            padding: 5px 0;
            position: relative;
        }

        body > header nav ul li a:hover {
            color: #fff;
        }

        body > header nav ul li a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #fff;
            bottom: 0;
            left: 0;
            transition: width 0.3s;
        }

        body > header nav ul li a:hover:after {
            width: 100%;
        }

        /* DESTAQUE PARA O LINK ATIVO */
        body > header nav ul li a[href="minha_conta.php"] {
            color: #fff;
            font-weight: 600;
        }

        body > header nav ul li a[href="minha_conta.php"]:after {
            width: 100%;
        }

        /* RESPONSIVIDADE */
        @media (max-width: 768px) {
            body > header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            body > header nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
        }

        /* ESTILOS GERAIS */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            min-height: 70vh;
        }

        /* CARD DA CONTA */
        .conta-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px auto;
            max-width: 800px;
        }

        .titulo-pagina {
            color: #333;
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .titulo-pagina:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background: #FFB22C;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        /* FORMULÁRIO */
        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
        }

        input:focus {
            border-color: #FFB22C;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 178, 44, 0.2);
        }

        /* BOTÕES */
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 1rem;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #FFB22C;
            color: #333;
        }

        .btn-primary:hover {
            background-color: #e6a028;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        /* MENSAGENS */
        .mensagem {
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            text-align: center;
            font-weight: 500;
        }

        .mensagem-sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensagem-erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* TIPO DE USUÁRIO */
        .tipo-usuario {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 5px;
        }

        .cliente {
            background-color: #e8f8ef;
            color: #27ae60;
        }

        .profissional {
            background-color: #fef5e7;
            color: #e67e22;
        }

        /* RESPONSIVO */
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .conta-card {
                padding: 20px;
                margin: 20px auto;
            }

            .btn-container {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="mensagem mensagem-sucesso"><?= htmlspecialchars($_SESSION['mensagem']) ?></div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="mensagem mensagem-erro"><?= htmlspecialchars($_SESSION['erro']) ?></div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <h1 class="titulo-pagina">Minha Conta</h1>

        <div class="conta-card">
            <form method="POST" action="minha_conta.php">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="senha">Nova Senha (deixe em branco para não alterar)</label>
                    <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres">
                    <small style="display: block; margin-top: 5px; color: #777;">Deixe vazio para manter a senha atual</small>
                </div>

                <div class="form-group">
                    <label>Tipo de Conta</label>
                    <div class="tipo-usuario <?= $usuario['tipo'] === 'profissional' ? 'profissional' : 'cliente' ?>">
                        <?= ucfirst($usuario['tipo']) ?>
                    </div>
                    <small style="display: block; margin-top: 5px; color: #777;">Para alterar o tipo de conta, entre em contato com o suporte</small>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <a href="logout.php" class="btn btn-danger">Sair da Conta</a>
                </div>
            </form>
        </div>
    </div>

    <?php require_once '../partes/footer.php'; ?>

    <script>
        // Validação simples do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;

            if (senha.length > 0 && senha.length < 8) {
                alert('A senha deve ter pelo menos 8 caracteres!');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
<?php
ob_end_flush(); // Envia o buffer de saída
?>