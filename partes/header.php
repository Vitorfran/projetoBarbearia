<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$current_section = isset($_GET['section']) ? $_GET['section'] : '';
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.
    min.css">

</head>
<body>
    <header>
        <div class="logo">
            <img src="../assets/imgs/header/Cortai.png" alt="Cortai">
        </div>

        <div class="menu-toggle">
            <img class="menu-hamb" src="../assets/imgs/menu.png" alt="Menu">
        </div>
        
        <div class="overlay"></div>

        <nav>
            <span class="close-btn"><i class="fas fa-times"></i></span>
            <ul>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <!-- Usuário logado -->
                    <li><a href="../pages/home_auth.php#home-secao-servicos" 
                          class="<?= ($current_page == 'home_auth.php' && $current_section == 'servicos') ? 'active' : '' ?>">Serviços</a></li>
                    <li><a href="../pages/home_auth.php#home-secao-contato"
                          class="<?= ($current_page == 'home_auth.php' && $current_section == 'contato') ? 'active' : '' ?>">Contato</a></li>
                    <li><a href="../pages/home_auth.php#home-secao-planos"
                          class="<?= ($current_page == 'home_auth.php' && $current_section == 'planos') ? 'active' : '' ?>">Planos</a></li>
                    <li><a href="../pages/meus_agendamentos.php"
                          class="<?= ($current_page == 'meus_agendamentos.php') ? 'active' : '' ?>">Meus Agendamentos</a></li>
                    <li><a href="../pages/minha_conta.php"
                          class="<?= ($current_page == 'minha_conta.php') ? 'active' : '' ?>">Minha Conta</a></li>
                    <li><a href="../pages/logout.php">Sair</a></li>
                <?php else: ?>
                    <!-- Usuário não logado -->
                    <li><a href="../public/home.php#home-secao-servicos"
                          class="<?= ($current_page == 'home.php' && $current_section == 'servicos') ? 'active' : '' ?>">Serviços</a></li>
                    <li><a href="../public/home.php#home-secao-contato"
                          class="<?= ($current_page == 'home.php' && $current_section == 'contato') ? 'active' : '' ?>">Contato</a></li>
                    <li><a href="../public/home.php#home-secao-planos"
                          class="<?= ($current_page == 'home.php' && $current_section == 'planos') ? 'active' : '' ?>">Planos</a></li>
                    <li><a href="../pages/login.php"
                          class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a></li>
                    <li><a href="../pages/cadastro.php"
                          class="<?= ($current_page == 'cadastro.php') ? 'active' : '' ?>">Cadastro</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <script>
    // Controle do menu mobile
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('nav');
        const overlay = document.querySelector('.overlay');
        const closeBtn = document.querySelector('.close-btn');
        
        // Controle de abertura/fechamento do menu
        function toggleMenu() {
            nav.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = nav.classList.contains('active') ? 'hidden' : 'auto';
        }
        
        menuToggle.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
        
        // Fechar menu ao clicar em links
        document.querySelectorAll('nav ul li a').forEach(link => {
            link.addEventListener('click', function() {
                if(window.innerWidth <= 768) {
                    toggleMenu();
                }
                
                // Atualiza a seção ativa para links de âncora
                if(this.href.includes('#')) {
                    const section = this.hash.replace('#home-secao-', '');
                    const url = new URL(this.href);
                    url.searchParams.set('section', section);
                    history.replaceState(null, null, url.toString());
                }
            });
        });
        
        // Verificação inicial para mobile
        function checkMobile() {
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'block';
                overlay.style.display = 'none';
                closeBtn.style.display = 'block';
                nav.style.display = 'none';
            } else {
                menuToggle.style.display = 'none';
                overlay.style.display = 'none';
                closeBtn.style.display = 'none';
                nav.style.display = 'block';
            }
        }
        
        checkMobile();
        window.addEventListener('resize', checkMobile);
        
        // Atualiza a seção ativa quando a página carrega com hash
        if(window.location.hash) {
            const section = window.location.hash.replace('#home-secao-', '');
            const url = new URL(window.location);
            url.searchParams.set('section', section);
            if(!window.location.search.includes('section=' + section)) {
                history.replaceState(null, null, url.toString());
            }
        }
    });
    </script>
</body>
</html>