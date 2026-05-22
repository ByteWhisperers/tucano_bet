<?php
header("Content-type: text/css");
require_once '../../includes/db.php';
include '../../includes/config.php';
?>
body {
            background-color: #121212;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        } 
        .body-no-scroll {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }   
       .top-bar {
            background-color: #1E1E1E;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            padding: 10px 0;
        }
        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
         }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px; 
        }
        .logo img {
            width: 100px;
            height: auto;
        }
        .menu-icon {
            font-size: 28px;
            color: <?= $corPrincipal ?>;
            cursor: pointer;
            user-select: none;
         }
        .menu-icon:hover {
            color: <?= $corHover ?>;
        }
        .buttons {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .button {
            padding: 11px 18px;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: <?= $corHover ?>;
        }
        .saldo-retirar {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .saldo {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }
        .btnRetirar {
            padding: 5px 20px;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .btnRetirar:hover {
            background-color: <?= $corHover ?>;
        }
        .bonus-resgatar {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .bonus-titulo, 
        .saldo-titulo {
            font-size: 14px;
            color: #fff;
            margin-bottom: 3px;
            font-weight: 500;
        }
        .bonus-valor {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }
        .btnResgatar {
            padding: 5px 20px;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .btnResgatar:hover {
            background-color: <?= $corHover ?>;
        }

        /* CSS Global */ 
        .container-conteudo {
            max-width: 1000px;
            margin: 10px auto 0;
            box-sizing: border-box;
            width: 100%;
        }
        .online-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin-right: 6px;
            background-color: #00ff00;
            border-radius: 50%;
            animation: piscar 1s infinite;
            vertical-align: middle;
        }
        @keyframes piscar {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; }
        }

        /* Sidebar menu */
        .sidebar {
            height: 100%;
            width: 210px;
            position: fixed;
            top: 0;
            left: -300px;
            background-color: #1E1E1E;
            overflow-x: hidden;
            transition: left 0.5s ease;
            padding: 60px 20px 0 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            z-index: 3;
        }
        .sidebar a {
            color: <?= $corTexto ?>;
            text-decoration: none;
            padding: 10px 15px;
            background-color: <?= $corPrincipal ?>;
            border-radius: 5px;
            transition: background-color 0.3s;
            cursor: pointer;
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .sidebar a:hover {
            background-color: <?= $corHover ?>;
        }
        .sidebar a i {
            margin-right: 5px; 
            color: <?= $corTexto ?>; 
        }
        .sidebar .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 34px;
            cursor: pointer;
            color: <?= $corPrincipal ?>;
            background-color: transparent;
            text-align: center;
            line-height: 32px;
        }
        .sidebar .close-btn:hover{
            color: <?= $corHover ?>;
        }

        /* Botão Esporte */
        .botao-esporte {
            width: 200px;
            height: 60px;
            background: <?= $corPrincipal ?>;
            border-bottom-right-radius: 25px;
            overflow: hidden;
            display: flex;
        }
        .botao-esporte .botao-esporte-card {
            width: 100%;
            height: 100%;
        }
        .botao-esporte .botao-esporte-btn {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            color: <?= $corTexto ?>;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .botao-esporte-btn i {
            margin-right: 5px;
        }
        .botao-esporte:hover {
            filter: brightness(1.1);
            background-color: <?= $corHover ?>;
        }

        /* Estilos para o slider */
        .slider-wrapper {
            max-width: 1000px;
            margin: 40px auto;
        }
        .slider-container {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .slide {
            flex: 0 0 100%;
            height: 200px;
        }
        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 3px;
        }
        .dots {
            text-align: center;
            margin-top: 10px;
        }
        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin: 0 5px;
            background-color: #bbb;
            border-radius: 50%;
            cursor: pointer;
        }
        .dot.active {
            background-color: <?= $corPrincipal ?>;
        }

        /* carrossel-ganhadores */
        .ganhos-container {
            display: flex;
            overflow: hidden;
            height: 80px;
            margin-top: 40px;

        }
        .ganhos-fixo {
            flex: 0 0 150px;
            background-color: #121212;
            color: white;
            font-weight: bold;
            text-align: left;
            display: flex;
            flex-direction: row; 
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 10px;
            box-sizing: border-box;
            height: 100%;
        }
        .ganhos-fixo i {
            font-size: 30px;
            color: #ffc107;
        }
        .ganhos-fixo span {
            line-height: 1.2;
        }
        .ganhos-rolando {
            flex: 1;
            overflow: hidden;
            position: relative;
            height: 100%;
        }
        .ganhos-slider {
            display: flex;
            align-items: center;
            height: 100%;
        }
        .card {
            display: flex;
            align-items: center;
            background-color: #1E1E1E;
            color: #fff;
            padding: 10px;
            margin-right: 15px;
            border-radius: 8px;
            min-width: 220px;
            height: 100%;
            box-sizing: border-box;
        }
        .card img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 4px;
            object-fit: contain;
            border-radius: 5px;
            background-color: transparent;
        }
        .valor {
            color: <?= $corPrincipal ?>;
            font-weight: bold;
        }
        
        /* Estilos para busca */ 
        .busca-container {
            margin: 40px auto 0 auto;
            text-align: center;
            width: 100%;
            max-width: 1000px;
        }
        .busca-form-row {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        .busca-input-icon {
            position: relative;
            width: 100%;
        }
        .busca-input-icon i {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: <?= $corPrincipal ?>;
        }
        .busca-input {
            width: 100%;
            padding: 6px 8px 6px 40px;
            border: none;
            border-radius: 3px;
            height: 50px;
            background-color: #1E1E1E;
            color: #fff;
            font-size: 16px;
        }
        .busca-input:focus {
            outline: none;
            border: 1px solid <?= $corPrincipal ?>;
            box-shadow: 0 0 5px <?= $corPrincipal ?>33; 
        }
        .busca-container,
        .busca-input-icon,
        .busca-input {
            box-sizing: border-box;
        }
        .busca-resultado {
            max-width: 600px;
            margin: 0 auto;
            text-align: left;
        }
        .busca-tabela-resultado {
            width: 100%;
            max-width: 1000px;
            margin: 5px auto;
            border-collapse: collapse;
            background-color: #1E1E1E;
            color: #fff;
            border-radius: 3px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            overflow: hidden;
            table-layout: fixed;
        }
        .busca-tabela-resultado td {
            padding: 8px 10px;
            vertical-align: middle;
            overflow: hidden;
        }
        .col-img {
            width: 60px;
            text-align: center;
            padding-left: 5px; 
            padding-right: 5px;
        }
        .col-nome {
            width: 45%;
            font-size: 16px;
        }
        .col-jogadores {
            overflow: visible !important;  
            white-space: normal !important; 
            text-overflow: clip !important; 
            font-size: 12px;
            text-align: center;
        }
        .col-jogar {
            width: 120px;
            text-align: right;
        }
        .busca-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 5px;
            background-color: transparent;
        }
        .btn-jogar {
            padding: 6px 12px;
            background-color: <?= $corPrincipal ?>;
            border: none;
            color: <?= $corTexto ?>;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        .btn-jogar:hover {
            background-color: <?= $corHover ?>;
        }
        .sem-resultado {
            text-align: center;
            color: #fff;
            padding: 20px;
            font-size: 16px;
            background-color: transparent;
        }

        /* Estilos para o modal */  
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 999;
        }
        .overlay.show {
            display: block;
        }
        .modal {
            display: none; 
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #1E1E1E;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            z-index: 1000;
            width: 90%;
            max-width: 400px;
            border-radius: 5px;
        } 
        .modal-content h2 {
            margin: 0 0 15px 0; 
            font-size: 24px;
            color: <?= $corPrincipal ?>;
            text-align: center; 
        }
        .modal.show {
            display: block;
        }
        .close-modal {
            position: absolute; 
            top: 10px; 
            right: 10px; 
            font-size: 18px; 
            color: <?= $corPrincipal ?>; 
            cursor: pointer;
        }
        .close-modal:hover {
            color: <?= $corHover ?>; 
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .form-row .input-icon {
            flex: 1;
        }
        .input-icon {
            position: relative;
            margin-bottom: 5px;
        }
        .input-icon i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: <?= $corPrincipal ?>; 
            pointer-events: none; 
        }
        .input-icon i:hover {
            color: <?= $corHover ?>; 
        }
        .input-icon input, 
        .input-icon textarea {
            padding-left: 35px; 
            width: 100%;
            height: 45px; 
            border:none; 
            border-radius: 3px; 
            color: #fff; 
            background-color: #121212;
            outline: none; 
            box-sizing: border-box;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        .input-icon input::placeholder, 
        .input-icon textarea::placeholder {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .submit-button {
            width: 100%;
            padding: 15px 20px; 
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-top: 0px; 
            font-size: 16px; 
            font-weight: bold;
        }
        .submit-button:hover {
            background-color: <?= $corHover ?>;
        } 
        input.submit-button.gerar-pix, 
        input.submit-button.dados,  
        input.submit-button.senha,  
        input.submit-button.contato, 
        input.submit-button.saque,
        button.submit-button.BotaoCopiaPix,
        input.submit-button.bonus{
            margin-top: 10px !important;
        }
        .msg-deposito, .msg-aposta, .msg-retirada {
            width: 100%;
            text-align: center;
            margin-top: 25px;
            flex-direction: row;
            align-items: center;
        }    
        .msg-deposito p, 
        .msg-aposta p, 
        .msg-retirada p{
            margin-top: -10px;
            font-size: 14px;
            color: #fff;
        }
        .msg-deposito strong, 
        .msg-aposta strong, 
        .msg-retirada strong{
         color: <?= $corPrincipal ?>;
        }
        .saldofomP, .RaspadinhafomP{
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
            text-align: center;
            color: #fff;
        } 
        .saldofomP strong, .RaspadinhafomP strong{
            color: <?= $corPrincipal ?>;
        }
        #alerta-deposito, 
        #alerta-retirada, 
        #alerta-dados, 
        #alerta-senha, 
        #alerta-contato, 
        #alerta-afiliados,
        #alerta-bonus,
        #alerta-raspadinha{
            width: 100%;
            margin-top: -10px;
            display: none;
            font-size: 14px;
        }
        .alertanao {
            width: 100%;
            color: #FE0000;
            text-align: center;
            display: block;
            cursor: pointer;
            line-height: 24px;
        }
        .alertasim {
            width: 100%;
            color: <?= $corPrincipal ?>;
            text-align: center;
            display: block;
            cursor: pointer;
            line-height: 24px;
        }
        .pix-container p{
            font-size: 14px;
            color: #fff;
        }
        .pix-container img {
            width: 150px;
            height: 150px;
            padding: 5px;
            border: 1px solid <?= $corPrincipal ?>;
            display: block;
            margin: 0 auto;
            margin-bottom: 20px;
            border-radius: 3px;
        }
         #pixLink::selection {
            background: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
        }
        .bonus-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 0 auto;
            max-width: 400px;
            text-align: left;
        }
        ul.bonus-status {
            list-style-type: disc;
            list-style-position: inside;
            padding: 0;
            margin: 0;
            color: #fff;
        }
        ul.bonus-status ul {
            list-style-type: circle;
            list-style-position: inside;
            padding: 0;
            margin: 5px 0 0 0;
        }
        ul.bonus-status li {
            color: #fff;
            margin: 3px 0;
            padding: 0;
            line-height: 1.4;
        }
        ul.bonus-status strong.valor {
            color: <?= $corPrincipal ?>;
            font-weight: bold;
        }
        ul.bonus-status li span.highlight {
            color: <?= $corPrincipal ?>;
            font-weight: bold;
        }
        ul.bonus-status li::marker {
            font-size: 1em;
        }

        /* Listagem dos jogos */  
            .lista-jogos, .aovivo-resultados {
            background-color: #121212;
            padding: 20px;
            margin-top: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .lista-jogos .jogos-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px; /* espaço entre os cards */
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }
        .titulo-lista-jogos, .aovivo-titulo {
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px;
            color: #fff;
        }
        .lista-jogos .jogo-card {
            background-color: #1E1E1E;
            height: 230px;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            background-size: contain;      
            background-repeat: no-repeat;   
            background-position: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .lista-jogos .jogo-card:hover {
            transform: scale(1.05);
        }
        .lista-jogos .jogo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: top center;
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
            transform: scale(0.94);
            transform-origin: center;
            border-radius: 10px;
            background-color: #1E1E1E;
        }
        .lista-jogos .jogo-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0,0,0,0.6);
            opacity: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s;
            cursor: default;
            z-index: 10;
        }
        .lista-jogos .jogo-card:hover .jogo-overlay {
            opacity: 1;
        }
        .lista-jogos .jogar-btn {
            background-color: <?= $corPrincipal ?>;
            border: none;
            color: <?= $corTexto ?>;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            z-index: 20;
            position: relative;
        }
        .lista-jogos .jogo-info {
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            width: 100%;
            color: white;
        }
        .lista-jogos .jogo-info .nome {
            font-size: 14px;
        }
        .lista-jogos .jogo-info .jogadores {
            margin-top: 4px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0px;
        }
        .jogadores-text {
            margin-left: 4px;
        }
        .btn-ver-mais {
            padding: 10px 20px;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            display: block;
            margin: 20px auto 0 auto;
            transition: background-color 0.3s ease;
        }
        .btn-ver-mais:hover {
            background-color: <?= $corHover ?>;
        }
    
        /* Iframe dos jogos */ 
        .modal-slots {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8); 
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-slots.show {
            display: flex;
        }
        .modal-slots-content {
            background-color: rgba(255, 255, 255, 0.1);
            width: 100%; 
            height: 100%; 
            position: relative;
            box-sizing: border-box; 
        }
        .modal-slots-content iframe {
            width: 100%;
            height: 100%;
            border: none;
            overflow: hidden; 
        }
        .modal-slots-content iframe::-webkit-scrollbar {
            display: none;
        }
        .modal-slots-content iframe {
            scrollbar-width: none;
        }
        .close-slots-modal {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            color: <?= $corTexto ?>;;
            cursor: pointer;
            border: none;
            background-color: <?= $corPrincipal ?>;
            border-radius: 5px;
            display: flex;
            justify-content: center;  
            align-items: center;      
            width: 30px;              
            height: 30px;             
            padding: 0;               
        }
        .close-slots-modal:hover i {
            background-color: <?= $corHover ?>;
        }

        /* Listagem ao vivos */
        .online-dot-red {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 6px;
            background-color: #FF3B30;
            border-radius: 50%;
            animation: piscarred 1s infinite;
            vertical-align: middle;
        }
        @keyframes piscarred {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        .aovivo-wrapper {
            width: 400px;
            margin: 0 auto;
        }
        #aovivo-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .aovivo-entry {
            padding: 10px;
            background: #1E1E1E;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            border-radius: 5px;
            color: white;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            width: 100%;
        }
        .aovivo-entry img {
            width: 40px;
            height: 40px;
            border-radius: 5px;
        }
        .aovivo-info {
            margin-left: 8px;
            font-size: 13px;
        }
        .aovivo-amount {
            color: <?= $corPrincipal ?>;
            font-weight: bold;
        }

        /* Termos boxs */ 
        .sidebartermo {
            position: fixed;
            left: -300px; 
            top: 0;
            width: 300px;
            height: 100%;
            background-color: #1E1E1E;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            transition: left 0.3s ease-in-out;
            z-index: 9999;
        }
        .sidebartermo.show {
            left: 0; 
        }
        .sidebartermo-content {
            position: absolute; 
            top: 40px; 
            bottom: 0; 
            overflow-y: auto; 
            padding: 15px;
            text-align: justify;
            font-size: 12px;
            color: #fff;
            scrollbar-width: thin;
            scrollbar-color: <?= $corPrincipal ?> #121212;
        }
        .sidebartermo-content::-webkit-scrollbar {
            width: 8px; 
        }
        .sidebartermo-content::-webkit-scrollbar-thumb {
            background-color: #121212; 
            border-radius: 10px; 
        }
        .sidebartermo-content::-webkit-scrollbar-thumb:hover {
            background-color: #121212;
        }
        .sidebartermo-content::-webkit-scrollbar-track {
            background: #121212;
        }
        .close-sidebartermo {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            color: <?= $corPrincipal ?>; 
            transition: color 0.3s;
        }
        .close-sidebartermo:hover {
            color: <?= $corHover ?>;
        }

          /* Estilos para o footer */ 
         .footer-line {
            border-top: 1px solid #444;
            margin: 30px auto;
            max-width: 1000px;
        } 
        .footer {
            color: #fff;
            width: 100%;
            padding: 40px 0 20px;
        }
        .footer .container-footer {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            flex-wrap: nowrap;
            gap: 5px;
        }
        .footer-column {
            flex: 1;
            min-width: 200px;
            margin: 10px;
        }
        .footer-column:first-child {
            display: flex;
            justify-content: center;   
            align-items: center;       
            min-height: 100%;          
        }
        .footer-column h4 {
            font-size: 16px;
            margin-bottom: 10px;
            color: <?= $corPrincipal ?>;
        }
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        .footer-column ul li {
            margin-bottom: 5px;
        }
        .footer-column ul li a {
            color: #ccc;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .footer-column ul li a:hover {
            text-decoration: none;
        }
        .footer img {
            max-width: 150px;
        }
        .footer img.pix-logo {
            max-width: 100px !important;
        }
        .social-icons a {
            margin-right: 10px;
            font-size: 24px;
            color: #fff;
            transition: color 0.3s;
        }
        .footer-text {
            max-width: 1000px;
            margin: 0 auto;
            font-size: 12px;
            color: #ccc;
            line-height: 1.6;
            padding: 0 20px;
        }
        .ver-mais-btn {
            padding: 10px 20px;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            display: block;
            margin: 10px auto 0;
            transition: background-color 0.3s;
        }
        .ver-mais-btn:hover {
            background-color: <?= $corHover ?>;
        }
        .footer-centered-img {
            max-width: 1000px;
            margin: 20px auto;
            text-align: center;
        }
        .selo-img {
            max-width: 350px !important; 
            height: auto !important;
        }
        .footer-bottom {
            max-width: 1000px;
            margin: 20px auto 0;
            text-align: center;
            font-size: 13px;
            color: #aaa;
        }
        .footer-bottom span {
            margin: 0 8px;
        }
         
        /* Páginas: Extrato - Afiliados */ 
        .extrato-table-container, 
        .afiliado-table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-top: 40px;
        }
        .extrato-table-container::-webkit-scrollbar, 
        .afiliado-table-container::-webkit-scrollbar {
        display: none;
        }
        .container-conteudo .afiliado-titulo{
            margin-top: 60px;
        }
        .afiliado-info {
            background: #1c1c1c;
            padding: 20px;
            border-radius: 5px;
            color: #BCBDC3;
            text-align: justify;
            margin-bottom: 40px;
        }
        .afiliado-info strong{
            color: <?= $corPrincipal ?>;
        }
        .afiliado-info .afiliado-content-link{
            margin-top: 20px;
            margin-bottom: 10px;
        }
        #link-afiliado::selection {
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
        }
        .extrato-titulo, .afiliado-titulo {
            font-size: 20px;
            font-weight: 600;
            color: <?= $corPrincipal ?>;
            margin-bottom: 10px;
            text-align: left;
            padding-left: 5px;
        }
        .extrato-table, 
        .afiliado-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
            background-color: #1c1c1c;
            border-radius: 10px;
            overflow: hidden;
        }
        .extrato-table th, .afiliado-table th,
        .extrato-table td, .afiliado-table td{
            padding: 12px 15px;
            text-align: center;
            font-size: 14px;
            border-bottom: 1px solid #333;
            color: #BCBDC3;
        }
        .extrato-table th, 
        .afiliado-table th {
            background-color: #232323;
            font-weight: 600;
        }
        .extrato-table td.tipo {
            color: <?= $corPrincipal ?>;
            font-weight: 500;
        }
        .extrato-table td.valor, 
        .afiliado-table td.valor {
            color: #4CAF50;
            font-weight: 600;
        }
        .afiliado-table td button.resgatar{
           display: inline-block; 
           padding: 5px 8px; 
           border-radius: 4px; 
           background-color: <?= $corPrincipal ?>; 
           color: <?= $corTexto ?>;
           border:none;
           cursor: pointer;
        }
        .extrato-pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination-btn {
            display: inline-block;
            padding: 8px 14px;
            margin: 0 4px;
            background-color: #2a2a2a;
            color: #BCBDC3;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .pagination-btn:hover {
            background-color: <?= $corHover ?>;
            color: #000;
        }
        .pagination-btn.active {
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            font-weight: bold;
        }

        /* Raspadinha */  
        #raspadinha-container {
            position: relative;
            width: 100%;
            height: 100%;
            aspect-ratio: 1 / 1;
            margin: 0 auto;
            border: 2px solid #1E1E1E;
            background: <?= $corPrincipal ?>;
            border-radius: 10px;
        }
        #raspadinha-numeros {
            position: absolute;
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            font-size: 24px;
            font-weight: bold;
            color: <?= $corTexto ?>;
            z-index: 0;
        }
        .raspadinha-numero {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0.5px solid #1E1E1E;
        }
        .raspadinha-numero:nth-child(1),
        .raspadinha-numero:nth-child(2),
        .raspadinha-numero:nth-child(3) {
            border-top: none;
        }
        .raspadinha-numero:nth-child(1),
        .raspadinha-numero:nth-child(4),
        .raspadinha-numero:nth-child(7) {
            border-left: none;
        }
        .raspadinha-numero:nth-child(3),
        .raspadinha-numero:nth-child(6),
        .raspadinha-numero:nth-child(9) {
            border-right: none;
        }
        .raspadinha-numero:nth-child(7),
        .raspadinha-numero:nth-child(8),
        .raspadinha-numero:nth-child(9) {
            border-bottom: none;
        }
        #raspadinha-canvas {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            cursor: pointer;
            border-radius: 10px;
            width: 100%;
            height: 100%;
        }
        #raspadinha-resultado {
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 15px;
            font-weight: bold;
            color: #00C774;
            text-align: center;
        }

    @media (max-width: 768px) {
        /* Estilos para topo */
        .container {
            max-width: 100%;
            padding: 0 8px;
        }
        .buttons {
            gap: 8px;
        }
        .button{
            font-size: 3.5vw;
            padding: 3.5vw 1.5vw;
            font-weight: bold;
        }
        .btnRetirar,
        .btnResgatar {
            font-size: 3vw;
            padding: 2vw 2vw;
        }
        .saldo,
        .bonus-valor {
            font-size: 3vw;
        }
        .bonus-titulo,
        .saldo-titulo {
            font-size: 3vw;
        }
        .content-box {
            width: calc(100% - 20px);
            margin: 30px auto;
        }

        /* Estilos para modals */
        .modal {
            width: calc(100% - 80px);
            max-width: none; 
        }
        .input-icon input {
            font-size: 16px;
        }

        /* Estilos para a busca */ 
        .busca-container {
            padding: 0 15px; 
        }
        .busca-input-icon {
            max-width: 100%;
        }
        .col-nome {
            width: 70%;
            font-size: 14px;
        }
        .col-jogadores {
            overflow: visible !important; 
            white-space: normal !important;
            text-overflow: clip !important;
            font-size: 12px;
            width: 40%;
            text-align: center;
        }
        .col-jogar {
            width: 80px;
            text-align: right;
        }

        /* Estilos para a lista de jogos */ 
        .lista-jogos{
            padding: 0px 0px 0px 5px;
        }
        .lista-jogos .jogos-container {
            display: flex;
            overflow-x: auto;
            padding-bottom: 10px;
            gap: 12px;
            width: 100%;
            flex-wrap: nowrap;
            -ms-overflow-style: none; 
            scrollbar-width: none; 
        }
        .lista-jogos .jogo-card {
            min-width: 120px; 
            height: 182px;
            flex-shrink: 0; 
        }
        .lista-jogos .jogos-container::-webkit-scrollbar {
            display: none; 
        }
        .lista-jogos .jogo-img {
            transform: scale(0.9);
        }
        .lista-jogos .jogo-info .nome {
            font-size: 12px;
        }
        .lista-jogos .jogar-btn {
            padding: 8px 16px;
            font-size: 13px;
        }

        /* Estilos para a lista de aovivo */    
        .aovivo-resultados{
             margin-top: -30px;
        }

        /* Estilos para o footer */ 
        .footer .container-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
        }
        .footer-column:nth-child(1) {
            display: none;
        }
        .footer-column {
            flex: 1 1 100%;
            box-sizing: border-box;
            margin: 5px 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer-column:nth-child(2),
        .footer-column:nth-child(3) {
            flex: 1 1 47%;
            order: 1;
        }
        .footer-column:nth-child(4),
        .footer-column:nth-child(5) {
            flex: 1 1 47%;
            order: 2;
        }
        .footer-column ul {
            padding-left: 0;
            list-style: none;
        }
        .footer-column img,
        .footer-column .pix-logo,
        .footer-column .social-icons {
             margin: 0 auto;
            display: block;
        }
        .footer-column h4 {
            text-align: center;
        }
        .social-icons {
            justify-content: center;
            display: flex;
        }

        /* Botão Esporte */ 
        .botao-esporte {
            width: 70%;
            height: 45px;
            position: fixed;
            z-index: 2; 
            border-radius: 0px 25px 25px 0px;
        }
        .botao-esporte .botao-esporte-btn {
            font-size: 15px;
        }
        .botao-esporte-btn i {
            margin-right: 8px;
        }

    }