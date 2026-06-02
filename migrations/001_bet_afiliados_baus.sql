CREATE TABLE IF NOT EXISTS bet_afiliados_baus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nivel INT NOT NULL,
  pessoas_necessarias INT NOT NULL,
  valor_recompensa DECIMAL(10,2) NOT NULL,
  status ENUM('bloqueado','disponivel','resgatado') DEFAULT 'bloqueado',
  data_desbloqueio DATETIME NULL,
  data_resgate DATETIME NULL,
  INDEX idx_usuario (usuario_id),
  UNIQUE KEY uk_usuario_nivel (usuario_id, nivel),
  FOREIGN KEY (usuario_id) REFERENCES bet_usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE bet_transacoes
  MODIFY bet_tipo ENUM('Deposito','Retirada','Bônus','Bônus Afiliação') NOT NULL;
