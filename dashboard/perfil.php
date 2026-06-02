<?php
if (!defined('IN_INDEX')) {
    header("Location: /dashboard/");
    exit();
}

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM bet_usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container-conteudo">
    <h2 class="perfil-titulo">Meu Perfil</h2>

    <!-- Abas de Navegação -->
    <div class="perfil-tabs">
        <button class="perfil-tab active" data-aba="meus-dados">Meus Dados</button>
        <button class="perfil-tab" data-aba="historico">Histórico Financeiro</button>
        <button class="perfil-tab" data-aba="seguranca">Segurança</button>
    </div>

    <!-- Aba: Meus Dados -->
    <div class="perfil-aba active" id="aba-meus-dados">
        <div class="perfil-section">
            <h3>Informações Pessoais</h3>
            
            <form id="formdados" action="php/dados.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_dados'] ?? '' ?>">
                
                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" value="<?= htmlspecialchars($usuario['bet_nome']) ?>" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" value="<?= htmlspecialchars($usuario['bet_cpf']) ?>" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="emaildados" id="emaildados" value="<?= htmlspecialchars($usuario['bet_email']) ?>" placeholder="Email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="celulardados" id="celulardados" value="<?= htmlspecialchars($usuario['bet_celular'] ?? '') ?>" placeholder="(XX)XXXXX-XXXX">
                    </div>
                </div>

                <div id="alerta-dados"></div>
                <input type="submit" id="subDados" class="submit-button" value="Salvar Alterações">
            </form>
        </div>
    </div>

    <!-- Aba: Histórico Financeiro -->
    <div class="perfil-aba" id="aba-historico">
        <div class="perfil-section">
            <h3>Histórico de Transações</h3>
            
            <div class="historico-table-container">
                <table class="historico-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Buscar histórico de transações
                        $stmt = $pdo->prepare("SELECT id, bet_valor, bet_tipo, bet_status, bet_data 
                                             FROM bet_transacoes 
                                             WHERE bet_usuario = ? 
                                             ORDER BY bet_data DESC 
                                             LIMIT 50");
                        $stmt->execute([$_SESSION['usuario_id']]);
                        $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($transacoes)):
                        ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">Nenhuma transação encontrada.</td>
                            </tr>
                        <?php else:
                            foreach ($transacoes as $trans):
                                $statusClass = strtolower($trans['bet_status']);
                                $statusClass = str_replace('cancelado', 'cancelado', $statusClass);
                        ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($trans['bet_data'])) ?></td>
                                <td><?= htmlspecialchars($trans['bet_tipo']) ?></td>
                                <td>R$ <?= number_format($trans['bet_valor'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge badge-<?= $statusClass ?>">
                                        <?= htmlspecialchars($trans['bet_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Aba: Segurança -->
    <div class="perfil-aba" id="aba-seguranca">
        <div class="perfil-section">
            <h3>Alterar Senha</h3>
            
            <form id="formsenha" action="php/senha.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token_senha'] ?? '' ?>">
                
                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="senha_atual" id="senha_atual" placeholder="Senha Atual" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="senha" id="senha" placeholder="Nova Senha" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirmasenha" id="confirmasenha" placeholder="Confirmar Nova Senha" required>
                    </div>
                </div>

                <div id="alerta-senha"></div>
                <input type="submit" id="subSenha" class="submit-button" value="Atualizar Senha">
            </form>
        </div>
    </div>
</div>

<style>
.perfil-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #ddd;
}

.perfil-tab {
    padding: 12px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.perfil-tab:hover {
    color: #00C774;
}

.perfil-tab.active {
    color: #00C774;
    border-bottom-color: #00C774;
}

.perfil-aba {
    display: none;
    animation: fadeIn 0.3s ease;
}

.perfil-aba.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.perfil-section {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.perfil-section h3 {
    margin-top: 0;
    color: #333;
    margin-bottom: 20px;
}

.historico-table-container {
    overflow-x: auto;
}

.historico-table {
    width: 100%;
    border-collapse: collapse;
}

.historico-table th,
.historico-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.historico-table th {
    background-color: #f0f0f0;
    font-weight: bold;
}

.historico-table tr:hover {
    background-color: #f9f9f9;
}

.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.badge-aprovado {
    background-color: #4CAF50;
    color: white;
}

.badge-pendente {
    background-color: #FFC107;
    color: #333;
}

.badge-cancelado {
    background-color: #f44336;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.perfil-tab');
    const abas = document.querySelectorAll('.perfil-aba');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const abaId = this.getAttribute('data-aba');
            
            // Remover classe active de todas as abas e tabs
            tabs.forEach(t => t.classList.remove('active'));
            abas.forEach(a => a.classList.remove('active'));
            
            // Adicionar classe active à aba e tab selecionados
            this.classList.add('active');
            document.getElementById('aba-' + abaId).classList.add('active');
        });
    });
});
</script>
