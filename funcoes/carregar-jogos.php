<?php
header('Content-Type: application/json');

require_once '../includes/db.php';

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit  = isset($_GET['limit'])  ? (int)$_GET['limit']  : 6;

$stmt = $pdo->prepare("
    SELECT 
        MIN(game_name) AS game_name, 
        MIN(game_img) AS game_img 
    FROM bet_jogos 
    WHERE game_ativado = 1 AND game_code != 'sport'
    GROUP BY game_code 
    ORDER BY RAND() 
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$jogos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($jogos);

exit;
