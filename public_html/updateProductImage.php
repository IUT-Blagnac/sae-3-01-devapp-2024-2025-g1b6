<?php
session_start();
include("connect.inc.php");
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idProd'])) {
        $idProd = $_POST['idProd'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'images/';
            $imageFileName = 'prod' . $idProd . '.png';
            $uploadFile = $uploadDir . $imageFileName;

            // Vérification du type de fichier
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'];
            $imageInfo = getimagesize($_FILES['image']['tmp_name']);
            
            if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes)) {
                throw new Exception('Format d\'image non supporté');
            }

            // Conversion en PNG si nécessaire
            $sourceImage = null;
            switch ($imageInfo['mime']) {
                case 'image/jpeg':
                case 'image/jpg':
                    $sourceImage = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($_FILES['image']['tmp_name']);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($_FILES['image']['tmp_name']);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($_FILES['image']['tmp_name']);
                    break;
            }

            if ($sourceImage) {
                // Préserver la transparence
                imagesavealpha($sourceImage, true);
                imagepng($sourceImage, $uploadFile);
                imagedestroy($sourceImage);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Image mise à jour avec succès'
                ]);
            } else {
                throw new Exception('Erreur lors du traitement de l\'image');
            }
        } else {
            throw new Exception('Aucune image fournie');
        }
    } else {
        throw new Exception('Requête invalide');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 