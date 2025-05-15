<?php
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/footer.css">
  <title>Agenda de Clientes</title>
  
</head>
<body>
  <!-- Seu conteúdo principal aqui -->
  <main>
    <!-- Conteúdo da sua aplicação -->
  </main>

  <!-- Footer Mobile First -->
  <footer>
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-section">
          <h3>Endereço</h3>
          <p>Rua fictícia Olinda, PE</p>
        </div>
        
        <div class="footer-section">
          <h3>Informações de Contato</h3>
          <p>Fale conosco</p>
          <a href="tel:99999999999">(99) 99999-9999</a>
          <a href="mailto:sualoja@gmail.com">sualoja@gmail.com</a>
        </div>
        
        <div class="footer-section">
          <h3>Horários</h3>
          <p>Segunda à Sábado</p>
          <p>das 8h às 19h</p>
        </div>
      </div>
      
      <div class="footer-divider"></div>
      
      <span class="footer-logo">
    <?php
    // Verifica se a sessão está ativa e se o usuário está logado
    $redirectUrl = (isset($_SESSION['usuario'])) ? '../pages/home_auth.php' : '../public/home.php';
    ?>
    <a href="<?php echo $redirectUrl; ?>">
        <img class="img-home" src="../assets/imgs/header/cortai.png" alt="Logo Cortaí">
    </a>
</span>
      
      <div class="social-media">
        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
          <img src="../assets/imgs/home/imagem-home-facebook.png" alt="Facebook">
        </a>
        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
          <img src="../assets/imgs/home/imagem-home-instagram.png" alt="Instagram">
        </a>
        <a href="https://whatsapp.com" target="_blank" rel="noopener noreferrer">
          <img src="../assets/imgs/home/imagem-home-whatsapp.png" alt="WhatsApp">
        </a>
        <a href="https://googlemaps.com" target="_blank" rel="noopener noreferrer">
          <img src="../assets/imgs/home/imagem-home-loc.png" alt="WhatsApp">
        </a>
      </div>
    </div>
      
      <div class="footer-copyright">
        <p> 2023 Cabelu. Todos os direitos reservados.</p>
        <p>É proibida a reprodução total ou parcial do conteúdo deste site sem autorização prévia.</p>
      </div>
    </div>
  </footer>
</body>
</html>