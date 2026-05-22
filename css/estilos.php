<?php
header("Content-type: text/css");
require_once '../includes/db.php';
include '../includes/config.php';
?>

        body {
            background-color: #121212;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Estilos para barra topo */
            .bonus-cadastro {
            width: 100%;
            position: relative;
            box-sizing: border-box;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            text-align: center;
            padding: 5px;
            font-size: 15px;
            font-weight: bold;
            z-index: 999;
        }

        /* Estilos para o topo */
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
        .logo img {
            width: 100px;
            height: auto;
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        .button{
            padding: 10px 20px;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
        }
        .button:hover {
            background-color: <?= $corHover ?>;
        } 

    /* Termos boxs */
        .sidebar {
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
        .sidebar.show {
            left: 0; 
        }
        .sidebar-content {
            position: absolute; 
            top: 40px; 
            bottom: 0; 
            overflow-y: auto; 
            padding: 15px;
            text-align: justify;
            font-size: 12px;
            color: #fff;
            /* Estilos para Firefox */
            scrollbar-width: thin;
            scrollbar-color: <?= $corPrincipal ?> #121212;
        }
        .sidebar-content::-webkit-scrollbar {
            width: 8px; 
        }
        .sidebar-content::-webkit-scrollbar-thumb {
            background-color: #121212; 
            border-radius: 10px; 
        }
        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background-color: #121212;
        }
        .sidebar-content::-webkit-scrollbar-track {
            background: #121212;
        }
        .close-sidebar {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            color: <?= $corPrincipal ?>; 
            transition: color 0.3s;
        }
        .close-sidebar:hover {
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
        .sem-resultado {
            text-align: center;
            color: #fff;
            padding: 20px;
            font-size: 16px;
            background-color: transparent;
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
        .input-icon input, .input-icon textarea {
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
        .input-icon input::placeholder, .input-icon textarea::placeholder {
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
        input.submit-button.recuperar, 
        input.submit-button.abrir-conta, 
        input.submit-button.contato {
            margin-top: 10px !important;
        }
        .termos {
            font-size: 12px;
            color: #fff; 
            line-height: 1;
            text-align: justify;
        }
        .termos a {
            color: <?= $corPrincipal ?>; 
            text-decoration: none; 
            font-weight: bold; 
            cursor: pointer;
        }
        .termos a:hover {
            text-decoration: underline; 
            color: <?= $corHover ?>;
        }
        .recover-password {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
            margin-top: 5px;
            cursor: pointer;
        }
        .recover-password a, 
        .log-in a, 
        .create-account a {
            color: <?= $corPrincipal ?>;
            text-decoration: none;
            font-size: 14px; 
        }
        .recover-password a:hover,
        .log-in a:hover,
        .create-account a:hover {
            color: <?= $corHover ?>;
        }
        .log-in, .create-account{
            width: 100%;
            display: flex;
            justify-content: center;
            margin-top: 15px;
            cursor: pointer;
        }
        #alerta-cadastro, #alerta-login, #alerta-senha, #alerta-contato {
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

        /* Estilos para o footer */  
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
        .footer-line {
            border-top: 1px solid #444;
            margin: 30px auto;
            max-width: 1000px;
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

    @media (max-width: 768px) {
            /* Estilos para barra topo */
            .bonus-cadastro {
                font-size: 3.5vw;
            }

            /* Estilos para o modal */ 
            .modal {
                width: calc(100% - 80px);
                max-width: none; 
            }
            .input-icon input {
                font-size: 16px;
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

            /* Botão Esporte */   
            .botao-esporte {
                width: 70%;
                height: 45px;
                position: fixed;
                z-index: 998; 
                border-radius: 0px 25px 25px 0px;
            }
            .botao-esporte .botao-esporte-btn {
                font-size: 15px;
            }
            .botao-esporte-btn i {
                margin-right: 8px;
            } 
}