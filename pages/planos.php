<?php
if (!isset($_SESSION['usuario'])) {
    header('Location: ../public/home.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Planos Mensais</title>
  <link rel="stylesheet" href="../assets/css/styleplanos.css" />
  
</head>
<body>

  <h1>Planos Mensais</h1>



  <div class="planos">
    <div class="plano">
      <div class="tesoura">✂</div>
      <h2>BÁSICO</h2>
      <div class="preco-box">
        <span class="moeda">R$</span>
        <div class="valor">29</div>
        <span class="mes">/mês</span>
        <div class="info">1 corte por mês</div>
      </div>
      <button class="selecionar-btn">SELECIONAR</button>
    </div>

    <div class="plano">
      <div class="tesoura">✂</div>
      <h2>PADRÃO</h2>
      <div class="preco-box">
        <span class="moeda">R$</span>
        <div class="valor">59</div>
        <span class="mes">/mês</span>
        <div class="info">2 cortes por mês</div>
      </div>
      
      <button class="selecionar-btn">SELECIONAR</button>
    </div>

    <div class="plano">
      <div class="tesoura">✂</div>
      <h2>PREMIUM</h2>
      <div class="preco-box">
        <span class="moeda">R$</span>
        <div class="valor">89</div>
        <span class="mes">/mês</span>
        <div class="info">4 cortes por mês</div>
      </div>
     
      <button class="selecionar-btn">SELECIONAR</button>
    </div>
  </div>

  <div class="cartoes">
    <button class="btn-cartao">ADICIONAR CARTÃO DE CRÉDITO <span>+</span></button>
    <button class="btn-cartao">ADICIONAR CARTÃO DE DÉBITO <span>+</span></button>

    <div class="cartao-salvo">
      <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="Mastercard">
      <span class="sifrao">MasterCard •••• •••• •••• 0435</span>
    </div>
  </div>
  <div class="voltar-container">
  <a href="../public/home.php" class="btn-voltar">← Voltar</a>
</div>

<?php require_once '../partes/footer.php';?>


</body>
</html>
