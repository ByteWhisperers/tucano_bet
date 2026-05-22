<?php
// Impede acesso direto via navegador (GET)
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    header("Location: /painel/dashboard/");
    exit;
}

session_name('adm_session');
session_start();

require_once '../../../includes/db.php';

// Autenticação AJAX
require_once 'auth_ajax_adm.php';

function valida_token_csrf($form_name) {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION["csrf_token_$form_name"]) && $token === $_SESSION["csrf_token_$form_name"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    if (!valida_token_csrf('logo')) {
        $errors[] = "Falha. Por favor, tente novamente!";
    }

    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Nenhum arquivo foi enviado!";
    } else {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($_FILES['logo']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Apenas PNG e JPEG são permitidos!";
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        $uploadDir = "../../../imagens/";
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('logo_', true) . '.' . $ext;
        $destination = $uploadDir . $newFileName;

        try {
            $pdo->beginTransaction();

            // Busca o nome do arquivo atual da logo
            $stmt = $pdo->prepare("SELECT bet_logo FROM bet_adm_config WHERE id = 1");
            $stmt->execute();
            $logoAtual = $stmt->fetchColumn();

            // Apaga o arquivo antigo se existir
            if ($logoAtual) {
                $caminhoAntigo = $uploadDir . $logoAtual;
                if (file_exists($caminhoAntigo)) {
                    unlink($caminhoAntigo);
                }
            }

            // Move o arquivo novo
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) {
                throw new Exception("Erro ao mover o arquivo.");
            }

            // Atualiza o banco com o novo nome
            $stmt = $pdo->prepare("UPDATE bet_adm_config SET bet_logo = :logo WHERE id = 1");
            $stmt->bindParam(':logo', $newFileName);
            $stmt->execute();

            $pdo->commit();

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Logo enviada com sucesso! <span><i class='fas fa-check'></i></span></p>"
            ];

            // Regenera token CSRF
            $_SESSION['csrf_token_logo'] = bin2hex(random_bytes(32));

        } catch (Exception $e) {
            $pdo->rollBack();

            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro: " . htmlspecialchars($e->getMessage()) . " <span><i class='fas fa-times'></i></span></p>"
            ];

            // Opcional: apagar arquivo recém-movido caso tenha ocorrido depois do move_uploaded_file
            if (file_exists($destination)) {
                unlink($destination);
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}