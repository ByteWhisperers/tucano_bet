<?php
header("Content-type: text/css");
include '../../../includes/db.php';
include '../../../includes/config.php';
?>
        body {
            background-color: #121212;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Container topo */
        .top-bar {
            background-color: #1E1E1E;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
            padding: 10px 0;
            position: relative;
            z-index: 2;
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
        .logo-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo img {
            width: 100px;
            height: auto;
        }
        .menu-icon {
            font-size: 28px;
            color: <?= $corPrincipal ?>;
            cursor: pointer;
        }

        .menu-icon:hover{
            color: <?= $corHover ?>;
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
        
        /* Container Global */ 
        .container-conteudo {
            max-width: 1000px;
            margin: 50px auto;
            box-sizing: border-box;
            width: 100%;
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
        .input-icon textarea, 
        .input-icon select{
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
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .toggle-row {
            display: flex;
            align-items: center;
            gap: 10px; 
            margin: 10px 0;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .switch-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0;
            right: 0; bottom: 0;
            background-color: #121212;
            transition: .4s;
            border-radius: 24px;
        }
        .switch-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        .toggle-switch input:checked + .switch-slider {
            background-color: <?= $corPrincipal ?>;
        }
        .toggle-switch input:checked + .switch-slider:before {
            transform: translateX(26px);
        }
        .toggle-label {
            vertical-align: middle;
            color: #fff;
            font-size: 14px;
        }
        .input-file-wrapper {
            position: relative;
            width: 100%;
        }
        .input-file-wrapper .icon-left {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: <?= $corPrincipal ?>;
            pointer-events: none;
            z-index: 20;
        }
        .input-file-wrapper .icon-right {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: <?= $corPrincipal ?>;
            pointer-events: none;
            z-index: 20;
        }
        .fake-file-input {
            display: block;
            width: 100%;
            height: 45px;
            padding-left: 40px;
            padding-right: 40px;
            background-color: #121212;
            border-radius: 3px;
            color: #fff;
            font-size: 14px;
            font-family: Arial, sans-serif;
            line-height: 45px;
            box-sizing: border-box;
            cursor: pointer;
            position: relative;
            user-select: none;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            z-index: 10;
        }
        #file-name-logo, 
        #file-name-favicon, 
        #file-name-slider {
            pointer-events: none;
            user-select: none;
            color: #999;
            transition: color 0.3s ease;
        }
        #file-name-logo.active, 
        #file-name-favicon.active, 
        #file-name-slider.active{
            color: white;
        }
        .input-file-wrapper input[type="file"] {
            display: none;
        }
        .progress-container {
            width: 100%;
            background-color: #121212;
            border-radius: 3px;
            margin: 10px 0;
            height: 10px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            width: 0%;
            background-color: <?= $corPrincipal ?>;
            transition: width 0.3s ease;
        }
        .color-option {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #999;
            cursor: pointer;
            margin-bottom: 5px;
            padding: 0 10px;
            width: 100%;
            height: 45px;
            border: none;
            border-radius: 3px;
            background-color: #121212;
            box-sizing: border-box;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        .color-option i {
            font-size: 16px;
            color: <?= $corPrincipal ?>;
        }
        .color-option input[type="radio"] {
            display: none;
        }
        .custom-radio {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid #1E1E1E;
            box-shadow: 0 0 3px rgba(0,0,0,0.5);
            transition: all 0.2s ease;
        }
        .color-option input[type="radio"]:checked + .custom-radio {
            outline: 1px solid #fff;
            outline-offset: 2px;
        }
        .color-label {
            transition: color 0.2s ease;
        }
        .color-option input[type="radio"]:checked ~ .color-label {
            color: #fff;
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
        .btn-cancelar {
            background-color: #e74c3c !important;
            color: #fff !important;
            margin-top: 10px;
        }
        .btn-cancelar:hover {
            background-color: #c0392b !important;
        }
        input.submit-button.espacobutton {
            margin-top: 10px !important;
        }
        .create-account a {
            color: <?= $corPrincipal ?>;
            text-decoration: none;
            font-size: 14px; 
        }
        .create-account a:hover {
            color: <?= $corHover ?>;
        }
        .create-account{
            width: 100%;
            display: flex;
            justify-content: center;
            margin-top: 15px;
            cursor: pointer;
            color: #fff;
            font-size: 12px;
        }
        .link-modal, .info-modal{
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            margin-top: 15px;
            color: #fff;
            font-size: 12px;
            cursor: default;
        }
        .info-modal p {
            margin: 0;      
            padding: 2px 0;  
            line-height: 1.4; 
        }
        .info-modal strong{
            color: <?= $corPrincipal ?>;
        }
        .link-modal p {
            margin: 5px 0;
            text-align: center;
        }
        .link-modal p:nth-of-type(2) {
            color: <?= $corPrincipal ?>;
            font-weight: bold;
            font-size: 12px;
        }
        .copy-btn {
            margin-top: 10px;
            padding: 5px 10px;
            cursor: pointer;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            border-radius: 5px;
            font-size: 12px;
        }
        .copy-btn:hover {
            background-color: <?= $corHover ?>;
        }
        #alerta-gerapix, 
        #alerta-playfiver,  
        #alerta-valores,
        #alerta-facebook,
        #alerta-email,
        #alerta-logo, 
        #alerta-favicon, 
        #alerta-slider, 
        #alerta-nomeurl, 
        #alerta-cores, 
        #alerta-redes, 
        #alerta-novojogo,
        #alerta-senha,
        #alerta-confirmar,
        #alerta-saldo-mais,
        #alerta-saldo-menos,
        #alerta-porcentagem-afiliado,
        #alerta-bonuscadastro,
        #alerta-afiliados,
        #alerta-cancelar,
        #alerta-despesas,
        #alerta-bonusraspadinha,
        #alerta-stopgames,
        #alerta-esportes,
        #alerta-emailmarketing,
        #alerta-excluircampanha,
        #alerta-campocel {
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

        /* Rodapé */
        footer {
            color: #555;
            text-align: center;
            padding: 15px;
            font-size: 14px;
        }

        /* CSS Páginas títulos */
        .titulo-funcoes, 
        .titulo-usuarios,
        .titulo-depositos, 
        .titulo-pagamentos, 
        .titulo-jogos,
        .titulo-despesas,
        .titulo-afiliados,
        .titulo-influenciadores,
        .titulo-marketing{
            margin-bottom: 15px;
            color: <?= $corPrincipal ?>;
        }
        .grid-boxes {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 10px;
        }
        .box-funcao {
            flex: 1 1 18%;
            min-width: 120px;
            padding: 20px;
            background: #1E1E1E;
            border-radius: 3px;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
        }
        .box-funcao i {
            font-size: 24px;
            margin-bottom: 10px;
            color: <?= $corPrincipal ?>;
        }
        .box-funcao span {
            font-size: 14px;
            font-weight: 500;
            color: #fff;
        }
        .box-funcao span.valor {
            margin-top: 8px; 
            font-size: 16px;
            font-weight: bold;
            color: #fff;
        }
        .box-funcao.negativo .valor {
            color: #e74c3c;
        }

        /* Páginas: JOGOS - PAGAMENTOS - DEPÓSITOS - DESPESAS - AFILIADOS - USUÁRIOS - INFLUENCIADORES */
        .influenciadores-table-wrapper,
        .usuarios-table-wrapper,
        .jogos-table-wrapper, 
        .pagamentos-table-wrapper, 
        .depositos-table-wrapper,
        .despesas-table-wrapper,
        .afiliados-table-wrapper,
        .emailmarketing-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .influenciadores-table-wrapper::-webkit-scrollbar,
        .jogos-table-wrapper::-webkit-scrollbar, 
        .pagamentos-table-wrapper::-webkit-scrollbar, 
        .depositos-table-wrapper::-webkit-scrollbar,
        .despesas-table-wrapper::-webkit-scrollbar,
        .afiliados-table-wrapper::-webkit-scrollbar,
        .emailmarketing-table-wrapper::-webkit-scrollbar{
            display: none;
        }
        .influenciadores-table,
        .jogos-table, 
        .pagamentos-table,
        .depositos-table,
        .despesas-table,
        .afiliados-table,
        .emailmarketing-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
            background-color: #1c1c1c;
            border-radius: 10px;
            overflow: hidden;
        }
        .jogos-table th, .pagamentos-table th, .depositos-table th, .despesas-table th, .afiliados-table th, .influenciadores-table th, .emailmarketing-table th,
        .jogos-table td, .pagamentos-table td, .depositos-table td, .despesas-table td, .afiliados-table td, .influenciadores-table td, .emailmarketing-table td{
            padding: 12px 15px;
            text-align: center;
            font-size: 14px;
            border-bottom: 1px solid #333;
            color: #BCBDC3;
        }
        .influenciadores-table th,
        .jogos-table th, 
        .pagamentos-table th, 
        .depositos-table th,
        .despesas-table th,
        .afiliados-table th,
        .influenciadores-table th {
            background-color: #232323;
            font-weight: 600;
        }
        .jogos-table td.jogo-nome {
            color: <?= $corPrincipal ?>;
            font-weight: 500;
        }
        .depositos-table td.status-aprovado{
            color: <?= $corPrincipal ?>;
        }
        .depositos-table td.status-pendente{
            color: #e74c3c;
        }
        .jogo-img {
            width: 35px;
            height: 45px;
            object-fit: contain;
            border-radius: 5px;
            background-color: #1c1c1c;
        }
        
        /* Página Email Marketing*/
        .btn-excluir-campanha {
            background: none;
            border: none;
            cursor: pointer;
            color: #e74c3c;
            font-size: 16px;
        }

        .btn-excluir-campanha:hover {
            opacity: 0.8;
        }

        .marketing-status {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .marketing-status .box {
            flex: 1;
            background: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
        }

        .marketing-status .box b {
            display: block;
            font-size: 22px;
            margin-top: 6px;
        }

        .marketing-status .box.enviado {
            background: <?= $corPrincipal ?>;
        }

        .marketing-status .box.pendente {
            background: <?= $corPrincipal ?>;
        }

        #excluir-campanha-nome {
            display: block;
            width: 100%;
            height: 45px;
            padding-left: 35px;
            line-height: 45px;
            border: none;
            border-radius: 3px;
            background-color: #121212;
            color: #fff;
            box-sizing: border-box;
            font-size: 14px;
            font-family: Arial, sans-serif;
            outline: none;
            text-align: left;
            user-select: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* CSS toggle Páginas JOGOS - PAGAMENTOS - USUÁRIOS - INFLUENCIADORES*/
        .switch, 
        .switch-pagamento, 
        .switch-usuario,
        .switch-influenciador{
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }
        .switch input, 
        .switch-pagamento input, 
        .switch-usuario input,
        .switch-influenciador input{
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider, 
        .slider-pagamento, 
        .slider-usuario,
        .slider-influenciador {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #555;
            transition: .4s;
            border-radius: 20px;
        }
        .slider:before, 
        .slider-pagamento:before, 
        .slider-usuario:before,
        .slider-influenciador:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        .switch input:checked + .slider,
        .switch-pagamento input:checked + .slider-pagamento,
        .switch-usuario input:checked + .slider-usuario, 
        .switch-influenciador input:checked + .slider-influenciador{
            background-color: <?= $corPrincipal ?>;
        }
        input.cancelado + .slider-pagamento {
            background-color: #e74c3c !important;
        }
        .switch input:checked + .slider:before,
        .switch-pagamento input:checked + .slider-pagamento:before,
        .switch-usuario input:checked + .slider-usuario:before,
        .switch-influenciador input:checked + .slider-influenciador:before {
            transform: translateX(20px);
        }

        /* Página USUÁRIO */
        .saldo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .saldo-valor {
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        .icon-menor {
            color: #e74c3c;
            cursor: pointer;
        }
        .icon-maior {
            color: <?= $corPrincipal ?>;
            cursor: pointer;
        }
        .percentual-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .percentual-valor {
            display: inline-block;
            min-width: 50px;
            text-align: center;
        }
        .icon-editar {
            color: <?= $corPrincipal ?>;
            cursor: pointer;
        }

        /* CSS Paginação - Páginas JOGOS - PAGAMENTOS - USUÁRIOS - DEPÓSITOS - INFLUENCIADORES*/
        .dashboard-pagination {
            text-align: center;
            margin-top: -20px;
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
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
        }
        .pagination-btn.active {
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            font-weight: bold;
        }
        .container-filtro {
            max-width: 1000px;
            margin: 40px auto;
            padding: 10px;
            background-color: #232323;
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
        }
        .container-filtro input,
        .container-filtro select,
        .container-filtro button {
            flex: 1 1 auto;
            height: 45px;
            padding: 8px 10px;
            background-color: #121212;
            border: none;
            border-radius: 3px;
            color: #fff;
            font-size: 14px;
            font-family: Arial, sans-serif;
            outline: none;
            box-sizing: border-box;
            white-space: nowrap;
        }
        .container-filtro select, 
        .input-icon select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #121212;
            color: #fff;
            background-image: url('data:image/svg+xml;utf8,<svg fill="white" height="16" viewBox="0 0 24 24" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 30px; /* Espaço para a seta */
        }
        select {
            color: #777;
            background-color: #121212;
        }
        .container-filtro button {
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .container-filtro button:hover {
            background-color: <?= $corHover ?>;
        }
        .botao-adicionar-jogo, .botao-adicionar-despesa, .botao-adicionar-campanha {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            padding: 12px 20px;
            background-color: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border-radius: 6px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s;
            box-sizing: border-box;
        }
        .botao-adicionar-jogo:hover, .botao-adicionar-despesa:hover, .botao-adicionar-campanha:hover {
            background-color: <?= $corHover ?>;
        }

        /* CSS Afiliados */
        .btn-expandir {
            color: <?= $corPrincipal ?>;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.2s, transform 0.2s;
        }
        .btn-expandir:hover {
            color: <?= $corHover ?>;
            transform: scale(1.2);
        }
        .scroll-expandido {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            background: #1c1c1c;
            scrollbar-width: thin;
            scrollbar-color: <?= $corPrincipal ?> #1e1e1e;
        }
        .scroll-expandido::-webkit-scrollbar {
            width: 8px;
        }
        .scroll-expandido::-webkit-scrollbar-thumb {
            background-color: <?= $corPrincipal ?>;
            border-radius: 10px;
        }
        .scroll-expandido::-webkit-scrollbar-thumb:hover {
            background-color: <?= $corHover ?>;
        }
        .scroll-expandido::-webkit-scrollbar-track {
            background: #1e1e1e;
        }
        .tabela-indicados {
            width: 100%;
            border-collapse: collapse;
        }
        .tabela-indicados th,
        .tabela-indicados td {
            padding: 6px 10px;
            border-bottom: 1px solid #333;
            font-size: 14px;
        }

        /* CSS Calendário */
        .flatpickr-calendar {
            background-color: #121212;
            color: <?= $corPrincipal ?>;
            border: none;
        }
        .flatpickr-months,
        .flatpickr-month,
        .flatpickr-current-month {
            background: transparent !important;
            border: none !important;
            color: <?= $corPrincipal ?>;
        }
        .flatpickr-current-month select,
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: transparent !important;
            color: <?= $corPrincipal ?> !important;
            border: none !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            padding-right: 0 !important;
            pointer-events: none;
            cursor: default;
        }
        .flatpickr-monthDropdown-months::-ms-expand {
            display: none !important;
        }
        .flatpickr-monthDropdown-months::after {
            display: none !important;
            content: none !important;
        }
        .flatpickr-current-month input.cur-year,
        .flatpickr-current-month .numInputWrapper {
            background: transparent !important;
            color: <?= $corPrincipal ?>;
            border: none;
            pointer-events: none;
            cursor: default;
        }
        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: <?= $corPrincipal ?>;
        }
        .flatpickr-weekdays,
        .flatpickr-weekday {
            background: transparent !important;
            color: <?= $corPrincipal ?>;
        }
        .flatpickr-day {
            background: #121212;
            color: #fff;
            border-radius: 50%;
        }
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: #1E1E1E;
            color: #fff;
            border: 2px solid <?= $corPrincipal ?>;
            box-sizing: border-box;
        }
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover {
            background: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            cursor: pointer;
        }
        .flatpickr-day:hover {
            background: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }
        .flatpickr-day.today {
            border:none;
            background: none;
            color: <?= $corPrincipal ?>;
        }
        .flatpickr-day.today:hover {
            background: <?= $corPrincipal ?>;
            color: <?= $corTexto ?>;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }
        .flatpickr-day.prevMonthDay:hover {
            background: #1E1E1E;
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }
        .flatpickr-day.nextMonthDay:hover {
            background: #1E1E1E;
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }
       .flatpickr-prev-month svg,
       .flatpickr-next-month svg {
            fill: <?= $corPrincipal ?> !important;
            transition: fill 0.3s ease;
        }
        .flatpickr-prev-month:hover svg,
        .flatpickr-next-month:hover svg {
            fill: <?= $corHover ?> !important;
            cursor: pointer;
        }
        input#bet_data,
        input.flatpickr-input {
            width: 100% !important;
            max-width: 100% !important;
            height: 45px !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            box-sizing: border-box !important;
            padding-left: 35px !important;
            border-radius: 6px !important;
        }

        /* CSS para Mobile */
    @media (max-width: 768px) {
        .container {
            width: 100%;
            padding: 0 10px;
        }

        .content-box {
            width: calc(100% - 20px);
            margin: 30px auto;
        }

        /* Estilos para o modal */ 
        .modal {
            width: calc(100% - 80px);
            max-width: none; 
        }
        .input-icon input {
            font-size: 16px;
        }

        /* Página FUNÇÕES */
        .box-funcao {
            width: 48%;
        }
        
        /* Página jOGOS */
        .jogos-table-wrapper{
            overflow-x: auto;
        }
    }