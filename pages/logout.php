<?php
// logout.php
session_start();

// Registra o logout antes de destruir a sessão (opcional)
if(isset($_SESSION['usuario'])) {
    $nomeUsuario = $_SESSION['usuario']['nome'];
    error_log("Usuário $nomeUsuario fez logout em " . date('Y-m-d H:i:s'));
}

// Limpa todos os dados da sessão
$_SESSION = array();

// Destrói o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Previne caching da página após logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redireciona para a página inicial
header("Location: ../public/home.php");
exit;
?>