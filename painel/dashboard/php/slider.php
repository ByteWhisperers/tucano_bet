<?php
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

    if (!valida_token_csrf('slider')) {
        $errors[] = "Falha. Por favor, tente novamente!";
    }

    if (!isset($_FILES['slider']) || count($_FILES['slider']['name']) !== 3) {
        $errors[] = "Envie exatamente 3 imagens!";
    }

    $uploadDir = "../../../imagens/";
    $nomesTemporarios = []; // Para guardar os dados das imagens válidas temporariamente
    $nomesFinais = [];

    if (empty($errors)) {
        // Validação das 3 imagens (todas antes de mover)
        foreach ($_FILES['slider']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['slider']['error'][$index] !== UPLOAD_ERR_OK) {
                $errors[] = "Erro no upload da imagem " . ($_FILES['slider']['name'][$index] ?? '') . ".";
                break;
            }

            $fileType = mime_content_type($tmpName);
            if (!in_array($fileType, ['image/png', 'image/jpeg'])) {
                $errors[] = "A imagem deve ser PNG ou JPEG!";
                break;
            }

            $imageSize = getimagesize($tmpName);
            if (!$imageSize || $imageSize[0] !== 1000 || $imageSize[1] !== 200) {
                $errors[] = "Cada imagem deve ter 1000x200 pixels!";
                break;
            }

            $ext = ($fileType === 'image/png') ? '.png' : '.jpg';
            $newFileName = uniqid("slider_", true) . $ext;

            $nomesTemporarios[] = [
                'tmp_name' => $tmpName,
                'final_name' => $newFileName
            ];
        }

        // Se passou na validação, move os arquivos para a pasta
        if (empty($errors)) {
            foreach ($nomesTemporarios as $imgData) {
                $destination = $uploadDir . $imgData['final_name'];

                if (!move_uploaded_file($imgData['tmp_name'], $destination)) {
                    $errors[] = "Erro ao mover a imagem " . $imgData['final_name'];
                    // Remove qualquer imagem já movida caso dê erro aqui
                    foreach ($nomesFinais as $img) {
                        $path = $uploadDir . $img;
                        if (file_exists($path)) unlink($path);
                    }
                    break;
                } else {
                    $nomesFinais[] = $imgData['final_name'];
                }
            }
        }
    }

    if (!empty($errors)) {
        $response = [
            "status" => "alertanao",
            "message" => "<p class='alertanao'>" . implode("<br>", $errors) . " <span><i class='fas fa-times'></i></span></p>"
        ];
    } else {
        try {
            $pdo->beginTransaction();

            // Remove imagens antigas
            $stmt = $pdo->prepare("SELECT bet_slider FROM bet_adm_config WHERE id = 1");
            $stmt->execute();
            $slidersAntigos = $stmt->fetchColumn();

            if ($slidersAntigos) {
                $imgs = explode(',', $slidersAntigos);
                foreach ($imgs as $img) {
                    $caminho = $uploadDir . trim($img);
                    if (file_exists($caminho)) unlink($caminho);
                }
            }

            // Salva novas imagens
            $novoValorSlider = implode(',', $nomesFinais);
            $stmt = $pdo->prepare("UPDATE bet_adm_config SET bet_slider = :slider WHERE id = 1");
            $stmt->bindParam(':slider', $novoValorSlider);
            $stmt->execute();

            $pdo->commit();

            $_SESSION['csrf_token_slider'] = bin2hex(random_bytes(32));

            $response = [
                "status" => "alertasim",
                "message" => "<p class='alertasim'>Slider atualizado com sucesso! <span><i class='fas fa-check'></i></span></p>"
            ];
        } catch (Exception $e) {
            $pdo->rollBack();

            // Remove novas imagens se algo falhar na transação
            foreach ($nomesFinais as $img) {
                $path = $uploadDir . $img;
                if (file_exists($path)) unlink($path);
            }

            $response = [
                "status" => "alertanao",
                "message" => "<p class='alertanao'>Erro: " . htmlspecialchars($e->getMessage()) . " <span><i class='fas fa-times'></i></span></p>"
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}