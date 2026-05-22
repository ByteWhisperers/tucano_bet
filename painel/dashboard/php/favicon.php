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

    if (!valida_token_csrf('favicon')) {
        $errors[] = "Falha. Por favor, tente novamente!";
    }

    if (!isset($_FILES['favicon']) || $_FILES['favicon']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Nenhum arquivo foi enviado!";
    } else {
        $fileTmpPath = $_FILES['favicon']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);

        // Aceita apenas PNG
        if ($fileType !== 'image/png') {
            $errors[] = "Apenas imagens PNG são permitidas!";
        }

        // Verifica dimensões (máximo 40x40)
        $imageSize = getimagesize($fileTmpPath);
        if (!$imageSize || $imageSize[0] > 48 || $imageSize[1] > 48) {
            $errors[] = "Imagem no máximo 48x48 pixels!";
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        $uploadDir = "../../../imagens/";
        $newFileName = uniqid('favicon_', true) . '.png';
        $destination = $uploadDir . $newFileName;

        try {
            $pdo->beginTransaction();

            // Busca o nome do favicon atual
            $stmt = $pdo->prepare("SELECT bet_favicon FROM bet_adm_config WHERE id = 1");
            $stmt->execute();
            $faviconAtual = $stmt->fetchColumn();

            // Remove favicon antigo
            if ($faviconAtual) {
                $caminhoAntigo = $uploadDir . $faviconAtual;
                if (file_exists($caminhoAntigo)) {
                    unlink($caminhoAntigo);
                }
            }

            // Move novo favicon
            if (!move_uploaded_file($_FILES['favicon']['tmp_name'], $destination)) {
                throw new Exception("Erro ao mover o arquivo.");
            }

            // Atualiza banco
            $stmt = $pdo->prepare("UPDATE bet_adm_config SET bet_favicon = :favicon WHERE id = 1");
            $stmt->bindParam(':favicon', $newFileName);
            $stmt->execute();

            $pdo->commit();

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Favicon enviado com sucesso! <span><i class='fas fa-check'></i></span></p>"
            ];

            // Regenera token CSRF
            $_SESSION['csrf_token_favicon'] = bin2hex(random_bytes(32));

        } catch (Exception $e) {
            $pdo->rollBack();

            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro: " . htmlspecialchars($e->getMessage()) . " <span><i class='fas fa-times'></i></span></p>"
            ];

            // Remove novo favicon se tiver sido salvo
            if (file_exists($destination)) {
                unlink($destination);
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}