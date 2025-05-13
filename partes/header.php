<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortai</title>
    <link rel="stylesheet" href="../assets/css/styleHeader.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../assets/imgs/header/Cortai.png" alt="Cortai">
        </div>
        <nav>
            <ul>
                <!-- Itens fixos para todos os usuários -->
                <li><a href="../public/home.php#home-secao-servicos">Serviços</a></li>
                <li><a href="../public/home.php#home-secao-contato">Contato</a></li>
                <li><a href="../public/home.php#home-secao-planos">Planos</a></li>
                
                <?php if (isset($_SESSION['usuario'])): ?>
                    <!-- ITENS PARA USUÁRIO LOGADO -->
                    <li><a href="../pages/meus_agendamentos.php">Meus Agendamentos</a></li>
                    <li><a href="../pages/minha_conta.php">Minha Conta</a></li>
                    <li><a href="../pages/logout.php">Sair</a></li>
                <?php else: ?>
                    <!-- ITENS PARA USUÁRIO NÃO LOGADO -->
                    <li><a href="../pages/login.php">Login</a></li>
                    <li><a href="../pages/cadastro.php">Cadastro</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>