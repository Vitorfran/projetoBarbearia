<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- <link rel="stylesheet" href="../assets/css/estilo.css"> -->
</head>
<body class="corpo">
    <main class="conteudo-principal">

        <section class="secao-principal">
            <div class="conteudo-texto">
                <h2 class="titulo-destaque">AGENDE SEU CORTE DE CABELO</h2>
                <h2 class="titulo-destaque">COM POUCOS CLIQUES</h2>
                <p class="descricao">Praticidade, estilo e pontualidade direto no seu celular.</p>
                <div class="botoes-navegacao">
                    <a href="./login.php" class="botao botao-entrar">ENTRAR</a>
                    <a href="./cadastro.php" class="botao botao-registrar">REGISTRAR</a>
                </div>
            </div>
            <div class="imagem-principal">
                <img src="../assets/imgs/home/imagem-home-barbeiro.png" alt="Corte de Cabelo" class="imagem">
            </div>
        </section>

        <section class="secao-servicos">
            <h1 class="titulo-secao">NOSSOS SERVIÇOS</h1>

            <div class="cartao-servico">
                <h3 class="titulo-servico">
                    CORTES MODERNOS 
                    <img src="../assets/imgs/home/imagem-home-tesoura.png" alt="Tesoura" class="icone-servico">
                </h3>
                <img src="../assets/imgs/home/imagem-home-corte-moderno.png" alt="Corte Moderno" class="imagem-servico">
                <p class="descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                <a href="#" class="botao botao-agendar">AGENDE</a>
            </div>

            <div class="cartao-servico">
                <h3 class="titulo-servico">
                    BARBAS 
                    <img src="../assets/imgs/home/imagem-home-navalha.png" alt="Navalha" class="icone-servico">
                </h3>
                <img src="../assets/imgs/home/imagem-home-barba.png" alt="Barba" class="imagem-servico">
                <p class="descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                <a href="#" class="botao botao-agendar">AGENDE</a>
            </div>

            <div class="cartao-servico">
                <h3 class="titulo-servico">
                    SOMBRANCELHAS 
                    <img src="../assets/imgs/home/imagem-home-cadeira.png" alt="Cadeira" class="icone-servico">
                </h3>
                <img src="../assets/imgs/home/imagem-home-sombrancelha.png" alt="Sombrancelha" class="imagem-servico">
                <p class="descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                <a href="#" class="botao botao-agendar">AGENDE</a>
            </div>
        </section>

        <section class="secao-contato">
            <h1 class="titulo-secao">FALE COM A GENTE</h1>
            <div class="conteudo-contato">
                <div class="formulario-contato">
                    <form action="" method="post" class="formulario">
                        <label for="nome" class="rotulo">Nome</label>
                        <input type="text" id="nome" name="nome" required class="campo-input">

                        <label for="email" class="rotulo">E-mail</label>
                        <input type="email" id="email" name="email" required class="campo-input">

                        <label for="mensagem" class="rotulo">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" rows="4" required class="campo-textarea"></textarea>

                        <button type="submit" class="botao botao-enviar">Enviar</button>
                    </form>
                </div>
                <div class="informacoes-contato">
                    <p class="item-contato">
                        <img src="../assets/imgs/home/imagem-home-telefone.png" alt="Telefone" class="icone-contato">
                        (81) 99999-9999
                    </p>
                    <p class="item-contato">
                        <img src="../assets/imgs/home/imagem-home-email.png" alt="Email" class="icone-contato">
                        contato@cortai.com
                    </p>
                    <ul class="lista-redes">
                        <li class="item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-instagram.png" alt="Instagram" class="icone-rede"></a></li>
                        <li class="item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-facebook.png" alt="Facebook" class="icone-rede"></a></li>
                        <li class="item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-whatsapp.png" alt="WhatsApp" class="icone-rede"></a></li>
                        <li class="item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-loc.png" alt="Localização" class="icone-rede"></a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="secao-planos">
            <h1 class="titulo-secao">NOSSOS PLANOS</h1>
            <div class="conteudo-planos">
                <h2 class="subtitulo-planos">Planos Mensais</h2>

                <div class="cartao-plano">
                    <h3 class="titulo-plano">Básico</h3>
                    <p class="preco-moeda">R$</p>
                    <p class="preco-valor">29/mês</p>
                    <p class="descricao-plano">1 corte por mês</p>
                    <a href="#" class="botao botao-selecionar">Selecionar</a>
                </div>

                <div class="cartao-plano">
                    <h3 class="titulo-plano">Padrão</h3>
                    <p class="preco-moeda">R$</p>
                    <p class="preco-valor">59/mês</p>
                    <p class="descricao-plano">2 cortes por mês</p>
                    <p class="descricao-plano">+ Sombrancelha</p>
                    <a href="#" class="botao botao-selecionar">Selecionar</a>
                </div>

                <div class="cartao-plano">
                    <h3 class="titulo-plano">Premium</h3>
                    <p class="preco-moeda">R$</p>
                    <p class="preco-valor">89/mês</p>
                    <p class="descricao-plano">4 cortes por mês</p>
                    <p class="descricao-plano">+ Barba & Sombrancelha</p>
                    <a href="#" class="botao botao-selecionar">Selecionar</a>
                </div>
            </div>
        </section>

        <section class="secao-depoimentos">
            <h1 class="titulo-secao">O QUE DIZEM NOSSOS CLIENTES</h1>
            <p class="descricao-depoimentos">Veja a opinião de quem já passou por aqui</p>
        </section>

    </main>
</body>
</html>
