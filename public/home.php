<?php
require_once '../partes/header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>



<!DOCTYPE html>
<!-- ===========================================
    CABEÇALHO DO DOCUMENTO HTML
=========================================== -->
<html lang="pt-br">
<head>
    <!-- Meta tags básicas -->
    <meta charset="UTF-8"> <!-- Define a codificação de caracteres -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura viewport para responsividade -->
    
    <!-- Título da página (aparece na aba do navegador) -->
    <title>Home</title>
    
    <!-- Links para folhas de estilo -->
    <!-- <link rel="stylesheet" href="../assets/css/home/reset.css"> --> <!-- Reset CSS (comentado) -->
    <link rel="stylesheet" href="../assets/css/fonts.css"> 
    <link rel="stylesheet" href="../assets/css/styleHeader.css"><!-- Estilos de fontes -->
    <link rel="stylesheet" href="../assets/css/home/home.css"> <!-- Estilos principais da página -->
</head>

<!-- ===========================================
    CORPO DA PÁGINA
=========================================== -->
<body class="home-corpo">
    <!-- Conteúdo principal da página -->
    <main class="home-conteudo-principal">
        
                <!-- ===========================================
                    SEÇÃO DE SERVIÇOS
                =========================================== -->
                <section id="home-secao-servicos" class="home-secao-servicos">
                    <!-- Título da seção -->
                    <h1 class="home-titulo-secao">NOSSOS SERVIÇOS</h1>
                    
                    <!-- Container dos cards de serviços -->
                    <div class="cards">
                        <!-- ===========================================
                            CARD 1 - CORTES MODERNOS
                        =========================================== -->
                        <div class="home-cartao-servico">
                            <!-- Cabeçalho do card (título + ícone) -->
                            <div class="home-cartao-servico-titulo">
                                <h3 class="home-titulo-servico">CORTES MODERNOS</h3>
                                <img src="../assets/imgs/home/imagem-home-tesoura.png" alt="Tesoura" class="home-icone-servico">
                            </div>
                            
                            <!-- Imagem do serviço -->
                            <img src="../assets/imgs/home/imagem-home-corte-moderno.png" alt="Corte Moderno" class="home-imagem-servico">
                            
                            <!-- Descrição do serviço -->
                            <p class="home-descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                            
                            <!-- Botão de ação -->
                            <a href="../pages/login.php" class="home-botao home-botao-agendar">AGENDE</a>
                        </div>
        
                        <!-- ===========================================
                            CARD 2 - BARBAS
                        =========================================== -->
                        <div class="home-cartao-servico">
                            <div class="home-cartao-servico-titulo">
                                <h3 class="home-titulo-servico">BARBAS</h3>
                                <img src="../assets/imgs/home/imagem-home-navalha.png" alt="Navalha" class="home-icone-servico">
                            </div>
        
                            <!-- Imagem do serviço -->
                            <img src="../assets/imgs/home/imagem-home-barba.png" alt="Barba" class="home-imagem-servico">
                            
                            <!-- Descrição do serviço -->
                            <p class="home-descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                            
                            <!-- Botão de ação -->
                            <a href="#../pages/login.php" class="home-botao home-botao-agendar">AGENDE</a>
                        </div>
        
                        <!-- ===========================================
                            CARD 3 - SOBRANCELHAS
                        =========================================== -->
                        <div class="home-cartao-servico">
                            <div class="home-cartao-servico-titulo">
                                <h3 class="home-titulo-servico">SOBRANCELHAS</h3>
                                <img src="../assets/imgs/home/imagem-home-cadeira.png" alt="Cadeira" class="home-icone-servico">
                            </div>
                            
                            <!-- Imagem do serviço -->
                            <img src="../assets/imgs/home/imagem-home-sombrancelha.png" alt="Sombrancelha" class="home-imagem-servico">
                            
                            <!-- Descrição do serviço -->
                            <p class="home-descricao-servico">Transforme seu visual com cortes modernos e personalizados, feitos para destacar sua personalidade e estilo único.</p>
                            
                            <!-- Botão de ação -->
                            <a href="../pages/login.php" class="home-botao home-botao-agendar">AGENDE</a>
                        </div>
                    </div>
                </section>

        <!-- ===========================================
            SEÇÃO PRINCIPAL (HERO SECTION)
        =========================================== -->
        <section class="home-secao-principal">
            <!-- Container do texto principal -->
            <div class="home-conteudo-texto">
                <!-- Títulos em destaque -->
                <h2 class="home-titulo-destaque">AGENDE SEU CORTE DE CABELO</h2>
                <h2 class="home-titulo-destaque">COM POUCOS CLIQUES</h2>
                
                <!-- Descrição -->
                <p class="home-descricao">Praticidade, estilo e pontualidade direto no seu celular.</p>
                
                <!-- Container dos botões de ação -->
                <div class="home-botoes-navegacao">
                    <!-- Botão de login -->
                    <a href="../pages/login.php" class="home-botao home-botao-entrar">ENTRAR</a>
                    <!-- Botão de cadastro -->
                    <a href="../pages/cadastro.php" class="home-botao home-botao-registrar">REGISTRAR</a>
                </div>
            </div>
            
            <!-- Container da imagem principal -->
            <div class="home-imagem-principal">
                <img src="../assets/imgs/home/imagem-home-barbeiro.png" alt="Corte de Cabelo" class="home-imagem">
            </div>
        </section>

        <!-- ===========================================
            SEÇÃO DE CONTATO
        =========================================== -->
        <section id="home-secao-contato" class="home-secao-contato">
            <!-- Título da seção -->
            <h1 class="home-titulo-secao">FALE COM A GENTE</h1>
            
            <!-- Container do conteúdo de contato -->
            <div class="home-conteudo-contato">
                <!-- ===========================================
                    FORMULÁRIO DE CONTATO
                =========================================== -->
                <div class="home-formulario-contato">
                    <form action="" method="post" class="home-formulario">
                        <!-- Campo Nome -->
                        <label for="nome" class="home-rotulo">Nome</label>
                        <div class="input-box">
                          <input type="text" required class="home-campo-input">  
                        </div>

                        <!-- Campo Email -->
                        <label for="email" class="home-rotulo">E-mail</label>
                        <div class="input-box">
                            <input type="email" required class="home-campo-input">
                        </div>

                        <!-- Campo Mensagem -->
                        <label for="mensagem" class="home-rotulo">Mensagem</label>
                        <div class="input-box">
                            <textarea rows="4" required class="home-campo-textarea"></textarea>
                        </div>

                        <!-- Botão de envio -->
                        <button type="submit" class="home-botao home-botao-enviar">Enviar</button>
                    </form>
                </div>
                
                <!-- ===========================================
                    INFORMAÇÕES DE CONTATO
                =========================================== -->
                <div class="home-informacoes-contato">
                    <!-- Telefone -->
                    <div>
                        <img src="../assets/imgs/home/imagem-home-telefone.png" alt="Telefone" class="home-icone-contato">
                        <p class="home-item-contato">(81) 99999-9999</p>
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <img src="../assets/imgs/home/imagem-home-email.png" alt="Email" class="home-icone-contato">
                        <p class="home-item-contato">contato@cortai.com</p>
                    </div>
                    
                    <!-- Redes Sociais -->
                    <ul class="home-lista-redes">
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-instagram.png" alt="Instagram" class="home-icone-rede"></a></li>
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-facebook.png" alt="Facebook" class="home-icone-rede"></a></li>
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-whatsapp.png" alt="WhatsApp" class="home-icone-rede"></a></li>
                        <li class="home-item-rede"><a href="#"><img src="../assets/imgs/home/imagem-home-loc.png" alt="Localização" class="home-icone-rede"></a></li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- ===========================================
            SEÇÃO DE PLANOS
        =========================================== -->
        <section id="home-secao-planos" class="home-secao-planos">
            <!-- Título da seção -->
            <h1 class="home-titulo-secao">NOSSOS PLANOS</h1>
            
            <!-- Container dos planos -->
            <div class="home-conteudo-planos">
                <!-- Subtítulo -->
                <h2 class="home-subtitulo-planos">Planos Mensais</h2>

                <!-- ===========================================
                    PLANO BÁSICO
                =========================================== -->
                <div class="home-cartao-plano">
                    <h3 class="home-titulo-plano">Básico</h3>
                    <p class="home-preco-moeda">R$</p>
                    <p class="home-preco-valor">29/mês</p>
                    <p class="home-descricao-plano">1 corte por mês</p>
                    <a href="../pages/planos.php" class="home-botao home-botao-selecionar">Selecionar</a>
                </div>

                <!-- ===========================================
                    PLANO PADRÃO
                =========================================== -->
                <div class="home-cartao-plano">
                    <h3 class="home-titulo-plano">Padrão</h3>
                    <p class="home-preco-moeda">R$</p>
                    <p class="home-preco-valor">59/mês</p>
                    <p class="home-descricao-plano">2 cortes por mês</p>
                    <p class="home-descricao-plano">+ Sombrancelha</p>
                    <a href="../pages/planos.php" class="home-botao home-botao-selecionar">Selecionar</a>
                </div>

                <!-- ===========================================
                    PLANO PREMIUM
                =========================================== -->
                <div class="home-cartao-plano">
                    <h3 class="home-titulo-plano">Premium</h3>
                    <p class="home-preco-moeda">R$</p>
                    <p class="home-preco-valor">89/mês</p>
                    <p class="home-descricao-plano">4 cortes por mês</p>
                    <p class="home-descricao-plano">+ Barba & Sombrancelha</p>
                    <a href="../pages/planos.php" class="home-botao home-botao-selecionar">Selecionar</a>
                </div>
            </div>
        </section>

        <!-- ===========================================
            SEÇÃO DE DEPOIMENTOS
        =========================================== -->
        <section id="home-secao-depoimentos" class="home-secao-depoimentos">
            <!-- Título da seção -->
            <h1 class="home-titulo-secao">O QUE DIZEM NOSSOS CLIENTES</h1>
            
            <!-- Descrição -->
            <p class="home-descricao-depoimentos">Veja a opinião de quem já passou por aqui</p>
            
            <!-- Depoimento 1 -->
            <h2><img src="../assets/imgs/home/imagem-home-foto-perfil-1.png" alt="foto de perfil">Leandro Costa</h2>
            <div>
                <p>Adorei o corte e o atendimento!</p>
                <p>⭐⭐⭐⭐⭐</p>
                <p>3 dias atrás</p>
            </div>
            
            <!-- Depoimento 2 -->
            <h2><img src="../assets/imgs/home/imagem-home-foto-perfil-2.png" alt="foto de perfil">Marcos Vinicius</h2>
            <div>
                <p>Ambiente muito confortável e um atendimento rápido</p>
                <p>⭐⭐⭐⭐⭐</p>
                <p>5 dias atrás</p>
            </div>
        </section>

    </main>

    <script>
// Observador de Intersecção para animar as seções
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('secao-visible');
            }
        });
    }, observerOptions);

    // Seleciona todas as seções para observar
    const sections = document.querySelectorAll('.home-secao-principal, .home-secao-servicos, .home-secao-contato, .home-secao-planos, .home-secao-depoimentos');
    
    sections.forEach(section => {
        observer.observe(section);
    });

    // Adiciona classe imediatamente para a primeira seção (hero)
    if (sections[0]) {
        sections[0].classList.add('secao-visible');
    }
});
</script>

<?php require_once '../partes/footer.php'; ?>

</body>
</html>