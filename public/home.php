<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- <link rel="stylesheet" href="../assets/css/home/reset.css"> -->
    <link rel="stylesheet" href="../assets/css/fonts.css">
    <link rel="stylesheet" href="../assets/css/home/home.css">
</head>
<body class="home-corpo">
    <main class="home-conteudo-principal">

        <section class="home-secao-principal">
            <div class="home-conteudo-texto">
                <h2 class="home-titulo-destaque">AGENDE SEU CORTE DE CABELO</h2>
                <h2 class="home-titulo-destaque">COM POUCOS CLIQUES</h2>
                <p class="home-descricao">Praticidade, estilo e pontualidade direto no seu celular.</p>
                <div class="home-botoes-navegacao">
                    <a href="../pages/login.php" class="home-botao home-botao-entrar">ENTRAR</a>
                    <a href="../pages/cadastro.php" class="home-botao home-botao-registrar">REGISTRAR</a>
                </div>
            </div>
            <div class="home-imagem-principal">
                <img src="../assets/imgs/home/imagem-home-barbeiro.png" alt="Corte de Cabelo" class="home-imagem">
            </div>
        </section>

        <section class="home-secao-servicos">
            <h1 class="home-titulo-secao">NOSSOS SERVIÇOS</h1>
            <div class="cards">
                <div class="home-cartao-servico">
                    <div class="home-cartao-servico-titulo">
                        <h3 class="home-titulo-servico">CORTES MODERNOS</h3>
                        <img src="../assets/imgs/home/imagem-home-tesoura.png" alt="Tesoura" class="home-icone-servico">
                    </div>
                    <img src="../assets/imgs/home/imagem-home-corte-moderno.png" alt="Corte Moderno" class="home-imagem-servico">
                    <p class="home-descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                    <a href="#" class="home-botao home-botao-agendar">AGENDE</a>
                </div>

                <div class="home-cartao-servico">
                    <div class="home-cartao-servico-titulo">
                        <h3 class="home-titulo-servico">BARBAS</h3>
                        <img src="../assets/imgs/home/imagem-home-navalha.png" alt="Navalha" class="home-icone-servico">
                    </div>

                    <img src="../assets/imgs/home/imagem-home-barba.png" alt="Barba" class="home-imagem-servico">
                    <p class="home-descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                    <a href="#" class="home-botao home-botao-agendar">AGENDE</a>
                </div>

                <div class="home-cartao-servico">
                    <div class="home-cartao-servico-titulo">
                        <h3 class="home-titulo-servico">SOBRANCELHAS</h3>
                        <img src="../assets/imgs/home/imagem-home-cadeira.png" alt="Cadeira" class="home-icone-servico">
                    </div>
                    <img src="../assets/imgs/home/imagem-home-sombrancelha.png" alt="Sombrancelha" class="home-imagem-servico">
                    <p class="home-descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                    <a href="#" class="home-botao home-botao-agendar">AGENDE</a>
                </div>
            </div>

        </section>

        <section class="home-secao-contato">
            <h1 class="home-titulo-secao">FALE COM A GENTE</h1>
            <div class="home-conteudo-contato">
                <div class="home-formulario-contato">
                    <form action="" method="post" class="home-formulario">
                        <label for="nome" class="home-rotulo">Nome</label>
                        <div class="input-box">
                          <input type="text" required class="home-campo-input">  
                        </div>

                        <label for="email" class="home-rotulo">E-mail</label>
                        <div class="input-box">
                            <input type="email" required class="home-campo-input">
                        </div>

                        <label for="mensagem" class="home-rotulo">Mensagem</label>
                        <div class="input-box">
                            <textarea rows="4" required class="home-campo-textarea"></textarea>
                        </div>

                        <button type="submit" class="home-botao home-botao-enviar">Enviar</button>
                    </form>
                </div>
                <div class="home-informacoes-contato">
                    <p class="home-item-contato">
                        <img src="../assets/imgs/home/imagem-home-telefone.png" alt="Telefone" class="home-icone-contato">
                        (81) 99999-9999
                    </p>
                    <p class="home-item-contato">
                        <img src="../assets/imgs/home/imagem-home-email.png" alt="Email" class="home-icone-contato">
                        contato@cortai.com
                    </p>
                    <ul class="home-lista-redes">
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-instagram.png" alt="Instagram" class="home-icone-rede"></a></li>
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-facebook.png" alt="Facebook" class="home-icone-rede"></a></li>
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-whatsapp.png" alt="WhatsApp" class="home-icone-rede"></a></li>
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-loc.png" alt="Localização" class="home-icone-rede"></a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="home-secao-planos">
            <h1 class="home-titulo-secao">NOSSOS PLANOS</h1>
            <div class="home-conteudo-planos">
                <h2 class="home-subtitulo-planos">Planos Mensais</h2>

                <div class="home-cartao-plano">
                    <h3 class="home-titulo-plano">Básico</h3>
                    <p class="home-preco-moeda">R$</p>
                    <p class="home-preco-valor">29/mês</p>
                    <p class="home-descricao-plano">1 corte por mês</p>
                    <a href="#" class="home-botao home-botao-selecionar">Selecionar</a>
                </div>

                <div class="home-cartao-plano">
                    <h3 class="home-titulo-plano">Padrão</h3>
                    <p class="home-preco-moeda">R$</p>
                    <p class="home-preco-valor">59/mês</p>
                    <p class="home-descricao-plano">2 cortes por mês</p>
                    <p class="home-descricao-plano">+ Sombrancelha</p>
                    <a href="#" class="home-botao home-botao-selecionar">Selecionar</a>
                </div>

                <div class="home-cartao-plano">
                    <h3 class="home-titulo-plano">Premium</h3>
                    <p class="home-preco-moeda">R$</p>
                    <p class="home-preco-valor">89/mês</p>
                    <p class="home-descricao-plano">4 cortes por mês</p>
                    <p class="home-descricao-plano">+ Barba & Sombrancelha</p>
                    <a href="#" class="home-botao home-botao-selecionar">Selecionar</a>
                </div>
            </div>
        </section>

        <section class="home-secao-depoimentos">
            <h1 class="home-titulo-secao">O QUE DIZEM NOSSOS CLIENTES</h1>
            <p class="home-descricao-depoimentos">Veja a opinião de quem já passou por aqui</p>
            <h2><img src="../assets/imgs/home/imagem-home-foto-perfil-1.png" alt="foto de perfil">Leandro Costa</h2>
            <div>
                <p>Adorei o corte e o atendimento!</p>
                <p>⭐⭐⭐⭐⭐</p>
                <p>3 dias atrás</p>
            </div>
            <h2><img src="../assets/imgs/home/imagem-home-foto-perfil-2.png" alt="foto de perfil">Marcos Vinicius</h2>
            <div>
                <p>Ambiente muito confortável e um atendimento rápido</p>
                <p>⭐⭐⭐⭐⭐</p>
                <p>5 dias atrás</p>
            </div>
        </section>

    </main>
</body>
</html>