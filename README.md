# 💈 Projeto Barbearia CORRAH

Este é um sistema web desenvolvido para uma barbearia fictícia chamado **CORRAH**, com o objetivo de facilitar o agendamento de cortes de cabelo, barbas e serviços relacionados. O projeto foi desenvolvido durante as aulas como atividade prática.

## 👨‍💻 Desenvolvedores

- **Rômulo Matheus**
- **Vitor Francisco**
- **José Victor**
- **Jamilly Rejane**
- **Giovanna Lopes**

---

## 🛠️ Tecnologias Utilizadas

- **HTML5** + **CSS3** – Estrutura e estilização do front-end
- **JavaScript** – Funcionalidades interativas
- **PHP** – Backend para lógica e conexão com o banco de dados
- **MySQL** – Banco de dados relacional

---

## 📁 Estrutura do Projeto

```
/assets
  └── css/
       └── home/
       └── styleHeader.css
       └── meusagendamentos.css
  └── js/
  └── imgs/
  └── vids/

/config
  └── database.php

/pages
  └── home.php
  └── login.php
  └── agendamento.php
  └── meus_agendamentos.php

[projetobarbearia.sql](./projetobarbearia.sql) (arquivo de criação do banco de dados)
```

---

## 🧪 Como Testar o Projeto

### 1. Requisitos

- Servidor local como **XAMPP** ou **WAMP**
- **PHP** e **MySQL** instalados

### 2. Configuração

1. Clone este repositório ou baixe o ZIP e extraia na pasta `htdocs` (se estiver usando XAMPP).
2. Inicie o Apache e o MySQL pelo painel do XAMPP.
3. Acesse o `phpMyAdmin` e **crie o banco de dados** com o nome:

```
projetobarbearia
```

4. Importe o arquivo `projetobarbearia.sql`, localizado na raiz do projeto. Esse arquivo irá criar as tabelas e inserir alguns dados iniciais:

- Tabelas: `usuarios`, `servicos`, `planos`, `agendamentos`, `indisponibilidades`, `horarios_trabalho`, `profissionais_servicos`
- Inserts iniciais para serviços: Barba, Sobrancelha, Cortes Modernos

5. No navegador, acesse:

```
http://localhost/nome-do-projeto/pages/home.php
```

---

## 🧾 Observações Importantes

- O login é dividido entre **clientes** e **profissionais**.
- O sistema está preparado para tratar **agendamentos**, **visualização de horários**, **cadastro de serviços**, e **planos**.
- O estilo visual é baseado nas cores e estética moderna, visando atrair o público jovem.

---

## 📷 Imagens do Projeto

### Tela inicial:
![Tela Inicial](imgs/tela_inicial.png)

### Tela de agendamentos:
![Tela de Agendamentos](imgs/tela_agendamentos.png)

---

## 📌 Licença

Projeto acadêmico sem fins lucrativos.
