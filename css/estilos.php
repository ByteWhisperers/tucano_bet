<?php
header("Content-type: text/css");
require_once '../includes/db.php';
include '../includes/config.php';
?>

body {
    background-color: var(--tropical-green-deep);
    margin: 0;
    padding: 0;
    font-family: 'Inter', Arial, sans-serif;
    color: var(--text-main);
}

/* Estilos para barra topo */
.bonus-cadastro {
    width: 100%;
    position: relative;
    box-sizing: border-box;
    background: var(--tropical-gradient);
    color: #000;
    text-align: center;
    padding: 8px;
    font-size: 14px;
    font-weight: 800;
    z-index: 999;
    text-transform: uppercase;
}

/* Estilos para o topo */
.top-bar {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    width: 100%;
    border-bottom: 1px solid var(--glass-border);
    padding: 15px 0;
}

.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-sizing: border-box;
}

.logo img {
    width: 120px;
    height: auto;
}

.buttons {
    display: flex;
    gap: 12px;
}

/* Redesign dos botões */
.button {
    padding: 10px 24px;
    background: var(--tropical-gradient);
    color: #000;
    border: none;
    border-radius: var(--border-radius-main);
    cursor: pointer;
    text-align: center;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.3s ease;
    text-transform: uppercase;
}

.button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px var(--tropical-gold-glow);
}

/* Sidebar Menu */
.sidebar {
    background: var(--tropical-green-deep);
    border-right: 1px solid var(--glass-border);
    box-shadow: 10px 0 30px rgba(0,0,0,0.5);
}

/* Slider Overrides */
.slide img {
    border-radius: var(--border-radius-main);
}

.dot.active {
    background-color: var(--tropical-gold);
    box-shadow: 0 0 10px var(--tropical-gold);
}

/* Cards de Jogos */
.lista-jogos .jogo-card {
    background-color: rgba(255,255,255,0.03);
    border: 1px solid var(--glass-border);
    border-radius: var(--border-radius-main);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.lista-jogos .jogo-card:hover {
    transform: translateY(-5px) scale(1.02);
    border-color: var(--tropical-gold);
    box-shadow: 0 10px 20px rgba(0,0,0,0.4);
}

.lista-jogos .jogar-btn {
    background: var(--tropical-gradient);
    color: #000;
    font-weight: 800;
    border-radius: 6px;
}

/* Busca */
.busca-input {
    background-color: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border);
    border-radius: var(--border-radius-main);
}

.busca-input:focus {
    border-color: var(--tropical-gold);
}

/* Footer */
.footer {
    background-color: rgba(0,0,0,0.3);
    border-top: 1px solid var(--glass-border);
    padding: 40px 0;
}

.footer-links a {
    color: var(--text-muted);
    transition: color 0.3s;
}

.footer-links a:hover {
    color: var(--tropical-gold);
}
