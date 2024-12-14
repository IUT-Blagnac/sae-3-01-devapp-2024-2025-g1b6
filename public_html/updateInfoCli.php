<?php 
session_start();
include("connect.inc.php");

// Vérification des conditions requises
if (!isset($_SESSION["user"]["IDCLIENT"])) {
    header("Location: connexion.php");
    exit();
}

if (!isset($_GET["typeModif"]) || 
    empty($_GET["typeModif"]) || 
    !($_GET["typeModif"] >= 1 && $_GET["typeModif"] <= 7) || 
    empty($_POST)) {
    header("Location: compte.php");
    exit();
}

$id_client = $_SESSION["user"]["IDCLIENT"];

// Séparation des différentes fonctionnalités dans des fonctions
function updatePersonalInfo($pdo, $id_client, $data) {
    $numTel = null;
    if (!empty($data["numTel"])) {
        $indicatif = str_replace("+", "00", $data["indicatif"]);
        $numero = $indicatif . $data["numTel"];
        $numTel = strlen($numero) > 11 ? $numero : null;
    }

    $query = $pdo->prepare("
        UPDATE CLIENT 
        SET NOMCLIENT = :nomCli,
            PRENOMCLIENT = :prenomCli,
            NUMTEL = :numTel,
            EMAIL = :emailCli,
            DATEN = :dateN
        WHERE IDCLIENT = :idClient
    ");

    $success = $query->execute([
        ":idClient" => $id_client,
        ":nomCli" => $data["nomCli"],
        ":prenomCli" => $data["prenomCli"],
        ":numTel" => $numTel,
        ":emailCli" => $data["emailCli"],
        ":dateN" => $data["dateN"]
    ]);

    if ($success) {
        $_SESSION["user"]["NOMCLIENT"] = $data["nomCli"];
        $_SESSION["user"]["PRENOMCLIENT"] = $data["prenomCli"];
        $_SESSION["user"]["NUMTEL"] = $numTel;
        $_SESSION["user"]["EMAIL"] = $data["emailCli"];
        $_SESSION["user"]["DATEN"] = $data["dateN"];
    }

    return $success;
}

function updatePassword($pdo, $id_client, $data) {
    $query = $pdo->prepare("SELECT PASSWORD FROM CLIENT WHERE IDCLIENT = :idClient");
    $query->execute([":idClient" => $id_client]);
    $currentPassword = $query->fetchColumn();

    if (!password_verify($data["ancien-mdp"], $currentPassword)) {
        return 'wrong_password';
    }

    $newPasswordHash = password_hash($data["nouveau-mdp"], PASSWORD_DEFAULT);
    $query = $pdo->prepare("
        UPDATE CLIENT 
        SET PASSWORD = :newPassword
        WHERE IDCLIENT = :idClient
    ");

    return $query->execute([
        ":idClient" => $id_client,
        ":newPassword" => $newPasswordHash
    ]);
}

function addAddress($pdo, $id_client, $data) {
    try {
        if (!validateAddressData($data)) {
            return false;
        }

        $pdo->beginTransaction();
        
        $query = $pdo->prepare("
            INSERT INTO ADRESSE (NUMRUE, NOMRUE, COMPLEMENTADR, NOMVILLE, CODEPOSTAL, PAYS)
            VALUES (:numRue, :nomRue, :complement, :ville, :codePostal, :pays)
        ");
        
        if (!$query->execute([
            ':numRue' => $data['numRue'],
            ':nomRue' => $data['nomRue'],
            ':complement' => !empty($data['complement']) ? $data['complement'] : null,
            ':ville' => $data['ville'],
            ':codePostal' => $data['codePostal'],
            ':pays' => $data['pays']
        ])) {
            throw new Exception();
        }
        
        $idAdresse = $pdo->lastInsertId();
        
        $query = $pdo->prepare("
            INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE)
            VALUES (:idClient, :idAdresse)
        ");
        
        if (!$query->execute([
            ':idClient' => $id_client,
            ':idAdresse' => $idAdresse
        ])) {
            throw new Exception();
        }
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}

function updateAddress($pdo, $id_client, $id_address, $data) {
    try {
        if (!validateAddressData($data)) {
            throw new Exception("Données d'adresse invalides");
        }

        // Vérifier que l'adresse appartient bien au client
        $query = $pdo->prepare("
            SELECT COUNT(*) FROM POSSEDERADR 
            WHERE IDCLIENT = :idClient AND IDADRESSE = :idAddress
        ");
        $query->execute([
            ':idClient' => $id_client,
            ':idAddress' => $id_address
        ]);
        
        if ($query->fetchColumn() == 0) {
            throw new Exception("Cette adresse n'appartient pas à ce client");
        }

        $query = $pdo->prepare("
            UPDATE ADRESSE 
            SET NUMRUE = :numRue,
                NOMRUE = :nomRue,
                COMPLEMENTADR = :complement,
                NOMVILLE = :ville,
                CODEPOSTAL = :codePostal,
                PAYS = :pays
            WHERE IDADRESSE = :idAddress
        ");

        return $query->execute([
            ':idAddress' => $id_address,
            ':numRue' => $data['numRue'],
            ':nomRue' => $data['nomRue'],
            ':complement' => !empty($data['complement']) ? $data['complement'] : null,
            ':ville' => $data['ville'],
            ':codePostal' => $data['codePostal'],
            ':pays' => $data['pays']
        ]);

    } catch (Exception $e) {
        return false;
    }
}

function deleteAddress($pdo, $id_client, $id_address) {
    try {
        $pdo->beginTransaction();

        // Vérifier que l'adresse appartient bien au client
        $query = $pdo->prepare("
            SELECT COUNT(*) FROM POSSEDERADR 
            WHERE IDCLIENT = :idClient AND IDADRESSE = :idAddress
        ");
        $query->execute([
            ':idClient' => $id_client,
            ':idAddress' => $id_address
        ]);
        
        if ($query->fetchColumn() == 0) {
            throw new Exception("Cette adresse n'appartient pas à ce client");
        }

        // Supprimer d'abord la liaison
        $query = $pdo->prepare("
            DELETE FROM POSSEDERADR 
            WHERE IDCLIENT = :idClient AND IDADRESSE = :idAddress
        ");
        
        if (!$query->execute([
            ':idClient' => $id_client,
            ':idAddress' => $id_address
        ])) {
            throw new Exception("Erreur lors de la suppression de la liaison");
        }

        // Puis supprimer l'adresse
        $query = $pdo->prepare("
            DELETE FROM ADRESSE 
            WHERE IDADRESSE = :idAddress
        ");
        
        if (!$query->execute([':idAddress' => $id_address])) {
            throw new Exception("Erreur lors de la suppression de l'adresse");
        }

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}

function validateAddressData($data) {
    return (
        filter_var($data['numRue'], FILTER_VALIDATE_INT) !== false &&
        $data['numRue'] > 0 &&
        strlen($data['numRue']) <= 10 &&
        strlen($data['nomRue']) <= 60 && 
        !empty($data['nomRue']) &&
        (!isset($data['complement']) || strlen($data['complement']) <= 60) &&
        strlen($data['ville']) <= 30 && 
        !empty($data['ville']) &&
        filter_var($data['codePostal'], FILTER_VALIDATE_INT) !== false &&
        $data['codePostal'] > 0 &&
        strlen($data['pays']) <= 30 && 
        !empty($data['pays'])
    );
}

function addPaymentCard($pdo, $id_client, $data) {
    try {
        if (!validateCardData($data)) {
            return false;
        }

        $pdo->beginTransaction();
        
        // Nettoyage du numéro de carte et du nom
        $numCB = str_replace(' ', '', $data['numCB']);
        $nomCompletCB = strtoupper(trim($data['nomCompletCB']));
        
        // Vérifier si la carte existe déjà
        $query = $pdo->prepare("
            SELECT NOMCOMPLETCB 
            FROM INFORMATIONPAIEMENT 
            WHERE NUMCB = :numCB
        ");
        $query->execute([':numCB' => $numCB]);
        $existingCard = $query->fetch();

        if ($existingCard) {
            // La carte existe déjà, comparons les noms
            $existingName = strtoupper(trim($existingCard['NOMCOMPLETCB']));
            
            if ($existingName === $nomCompletCB) {
                // Les noms correspondent, on ajoute juste la liaison
                $query = $pdo->prepare("
                    INSERT INTO POSSEDERIP (NUMCB, IDCLIENT)
                    VALUES (:numCB, :idClient)
                ");
                
                if (!$query->execute([
                    ':numCB' => $numCB,
                    ':idClient' => $id_client
                ])) {
                    throw new Exception("Erreur lors de la liaison de la carte au client");
                }
                
                $pdo->commit();
                return true;
            } else {
                // Les noms ne correspondent pas
                $pdo->rollBack();
                $_SESSION['card_error'] = 'wrong_holder';
                return false;
            }
        }
        
        // La carte n'existe pas, on l'ajoute normalement
        $dateExp = date('Y-m-t', strtotime($data['dateExp'] . '-01'));
        
        $query = $pdo->prepare("
            INSERT INTO INFORMATIONPAIEMENT (NUMCB, NOMCOMPLETCB, DATEEXP, CRYPTOGRAMME)
            VALUES (:numCB, :nomCompletCB, :dateExp, :cryptogramme)
        ");
        
        if (!$query->execute([
            ':numCB' => $numCB,
            ':nomCompletCB' => $nomCompletCB,
            ':dateExp' => $dateExp,
            ':cryptogramme' => $data['cryptogramme']
        ])) {
            throw new Exception("Erreur lors de l'ajout de la carte");
        }
        
        // Liaison avec le client
        $query = $pdo->prepare("
            INSERT INTO POSSEDERIP (NUMCB, IDCLIENT)
            VALUES (:numCB, :idClient)
        ");
        
        if (!$query->execute([
            ':numCB' => $numCB,
            ':idClient' => $id_client
        ])) {
            throw new Exception("Erreur lors de la liaison de la carte au client");
        }
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}


function validateCardData($data) {
    // Nettoyage du numéro de carte
    $numCB = str_replace(' ', '', $data['numCB']);
    
    $validation = [
        'numCB' => preg_match('/^[0-9]{16}$/', $numCB),
        'nomCompletCB' => strlen($data['nomCompletCB']) <= 50 && !empty($data['nomCompletCB']),
        'cryptogramme' => preg_match('/^[0-9]{3}$/', $data['cryptogramme']),
        'dateExp' => strtotime($data['dateExp']) > time()
    ];

    error_log("Validation carte: " . json_encode($validation));
    
    return !in_array(false, $validation, true);
}

function deletePaymentCard($pdo, $id_client, $numCB) {
    try {
        $pdo->beginTransaction();

        // Vérifier que la carte appartient bien au client
        $query = $pdo->prepare("
            SELECT COUNT(*) FROM POSSEDERIP 
            WHERE IDCLIENT = :idClient AND NUMCB = :numCB
        ");
        $query->execute([
            ':idClient' => $id_client,
            ':numCB' => $numCB
        ]);
        
        if ($query->fetchColumn() == 0) {
            throw new Exception("Cette carte n'appartient pas à ce client");
        }

        // Supprimer d'abord la liaison
        $query = $pdo->prepare("
            DELETE FROM POSSEDERIP 
            WHERE IDCLIENT = :idClient AND NUMCB = :numCB
        ");
        
        if (!$query->execute([
            ':idClient' => $id_client,
            ':numCB' => $numCB
        ])) {
            throw new Exception("Erreur lors de la suppression de la liaison");
        }

        // Puis supprimer la carte
        $query = $pdo->prepare("
            DELETE FROM INFORMATIONPAIEMENT 
            WHERE NUMCB = :numCB
        ");
        
        if (!$query->execute([':numCB' => $numCB])) {
            throw new Exception("Erreur lors de la suppression de la carte");
        }

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}

// Traitement principal
$success = false;
$error = null;

switch ($_GET["typeModif"]) {
    case 1: // Modification infos personnelles
        $success = updatePersonalInfo($pdo, $id_client, $_POST);
        break;
        
    case 2: // Modification mot de passe
        $result = updatePassword($pdo, $id_client, $_POST);
        if ($result === 'wrong_password') {
            header("Location: compte.php?modif=erreur&typeModif=2&error=wrong_password");
            exit();
        }
        $success = $result;
        break;
        
    case 3: // Ajout adresse
        $success = addAddress($pdo, $id_client, $_POST);
        break;
        
    case 4: // Modification adresse
        if (!isset($_POST['idAddress'])) {
            header("Location: compte.php?modif=erreur&typeModif=4");
            exit();
        }
        $success = updateAddress($pdo, $id_client, $_POST['idAddress'], $_POST);
        break;
        
    case 5: // Suppression adresse
        if (!isset($_POST['idAddress'])) {
            header("Location: compte.php?modif=erreur&typeModif=5");
            exit();
        }
        $success = deleteAddress($pdo, $id_client, $_POST['idAddress']);
        break;
    case 6: // Ajout carte bancaire
        $success = addPaymentCard($pdo, $id_client, $_POST);
        if (!$success && isset($_SESSION['card_error']) && $_SESSION['card_error'] === 'wrong_holder') {
            unset($_SESSION['card_error']);
            header("Location: compte.php?modif=erreur&typeModif=6&error=wrong_holder");
            exit();
        }
        break;
    case 7: // Suppression carte
        if (!isset($_POST['numCB'])) {
            header("Location: compte.php?modif=erreur&typeModif=7");
            exit();
        }
        $success = deletePaymentCard($pdo, $id_client, $_POST['numCB']);
        break;
}

header("Location: compte.php?modif=" . ($success ? "ok" : "erreur") . "&typeModif=" . $_GET["typeModif"]);
exit();
?>