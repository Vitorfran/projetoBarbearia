<?php
session_start(); // Inicia a sessão (necessário para verificar login)

// Se o usuário estiver logado, vai para home_auth.php
if (isset($_SESSION['usuario_logado'])) {
    header('Location: pages/home_auth.php');
} 
// Se não estiver logado, vai para public/home.php (ou login.php)
else {
    header('Location: public/home.php'); // ou pages/login.php
}

exit;
?>