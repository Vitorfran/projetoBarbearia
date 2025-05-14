<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agenda de Clientes</title>
  <link rel="stylesheet" href="../assets/css/styleTelaBarbeiro.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
  <header>
      <div class="logo">
          <img src="../assets/imgs/header/Cortai.png" alt="Cortai">      
            <h1>Olá, Lucas!</h1>
      </div>
      <nav>
        <a href="#">Agenda de clientes</a>
        <a href="#">Declarar Ausência</a>
      </nav>
    </div>
  </header>

  <main>
    <section class="agenda">
      <table class="table">
        <thead>
          <tr>
            <th>cliente</th>
            <th>serviço</th>
            <th>data e hora</th>
            <th>presença</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>####</td>
            <td>####</td>
            <td>####</td>
            <td><span class="icon">🔖</span></td>
          </tr>
          <tr>
            <td>####</td>
            <td>####</td>
            <td>####</td>
            <td><span class="icon">🔖</span></td>
          </tr>
          <tr>
            <td>####</td>
            <td>####</td>
            <td>####</td>
            <td><span class="icon">🔖</span></td>
          </tr>
        </tbody>
      </table>
    </section>

    <section class="ausencia">
      <h2>Declarar Ausência</h2>
      <form>
        <label for="motivo">Motivo</label>
        <textarea id="motivo" name="motivo" placeholder="Digite aqui..."></textarea>
        <button type="submit">Enviar</button>
      </form>
    </section>
  </main>
</body>
</html>
