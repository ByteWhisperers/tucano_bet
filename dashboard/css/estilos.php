<?php
header("Content-type: text/css");
require_once '../../includes/db.php';
include '../../includes/config.php';
?>

body {
    background-color: var(--tropical-green-deep);
    margin: 0;
    padding: 0;
    font-family: 'Inter', Arial, sans-serif;
    color: var(--text-main);
}

.top-bar {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--glass-border);
    padding: 15px 0;
}

.sidebar {
    background: var(--tropical-green-deep) !important;
    border-right: 1px solid var(--glass-border);
}

.sidebar a {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid var(--glass-border);
    border-radius: 8px !important;
    margin-bottom: 12px !important;
    font-weight: 600;
}

.sidebar a:hover {
    background: var(--tropical-gradient) !important;
    color: #000 !important;
    border-color: var(--tropical-gold);
}

.sidebar a i {
    color: var(--tropical-gold) !important;
}

.sidebar a:hover i {
    color: #000 !important;
}

/* Saldo e Bônus */
.saldo, .bonus {
    color: var(--tropical-gold) !important;
    font-size: 20px !important;
    text-shadow: 0 0 10px var(--tropical-gold-glow);
}

.btnRetirar, .btnBonus, .submit-button {
    background: var(--tropical-gradient) !important;
    color: #000 !important;
    font-weight: 800 !important;
    border-radius: 8px !important;
    border: none !important;
    text-transform: uppercase;
}

/* Modais */
.modal-content {
    background: var(--tropical-green-deep) !important;
    border: 1px solid var(--tropical-gold) !important;
    box-shadow: 0 0 30px rgba(0,0,0,0.8);
}

.modal-content h2 {
    color: var(--tropical-gold) !important;
    font-weight: 800;
}

.form-row input {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid var(--glass-border) !important;
    color: #fff !important;
    border-radius: 8px !important;
}

.form-row input:focus {
    border-color: var(--tropical-gold) !important;
}

/* Tabelas */
.afiliado-table-container, .historico-table-container {
    background: var(--glass-bg);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid var(--glass-border);
}

.afiliado-table th, .historico-table th {
    color: var(--tropical-gold);
    border-bottom: 2px solid var(--glass-border);
}

/* Baús UI Improvements */
.baus-section {
    background: rgba(0,0,0,0.2);
    border-radius: 15px;
    padding: 25px;
    margin: 20px 0;
    border: 1px solid var(--glass-border);
}

.bau-card {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid var(--glass-border);
}

.bau-card.bau-disponivel {
    border-color: var(--tropical-gold) !important;
    background: rgba(255, 215, 0, 0.05) !important;
}
