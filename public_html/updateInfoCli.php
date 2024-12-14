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
        !($_GET["typeModif"] >= 1 && $_GET["typeModif"] <= 5) || 
        empty($_POST)) {
        header("Location: compte.php");
        exit();
    }

    $id_client = $_SESSION["user"]["IDCLIENT"];

    // Traitement de la modification des informations personnelles
    if ($_GET["typeModif"] == 1) {
        // Préparation du numéro de téléphone
        $numTel = null;
        if (!empty($_POST["numTel"])) {
            $indicatif = str_replace("+", "00", $_POST["indicatif"]);
            $numero = $indicatif . $_POST["numTel"];
            $numTel = strlen($numero) > 11 ? $numero : null;
        }

        // Mise à jour des informations
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
            ":nomCli" => $_POST["nomCli"],
            ":prenomCli" => $_POST["prenomCli"],
            ":numTel" => $numTel,
            ":emailCli" => $_POST["emailCli"],
            ":dateN" => $_POST["dateN"]
        ]);

        // Redirection avec message de statut
        if ($success) {
            // Mise à jour de la session avec les nouvelles valeurs
            $_SESSION["user"]["NOMCLIENT"] = $_POST["nomCli"];
            $_SESSION["user"]["PRENOMCLIENT"] = $_POST["prenomCli"];
            $_SESSION["user"]["NUMTEL"] = $numTel;
            $_SESSION["user"]["EMAIL"] = $_POST["emailCli"];
            $_SESSION["user"]["DATEN"] = $_POST["dateN"];
            
            header("Location: compte.php?modif=ok&typeModif=1");
        } else {
            header("Location: compte.php?modif=erreur&typeModif=1");
        }
        exit();
    }else if ($_GET["typeModif"] == 2) { // Traitement de la modification du mot de passe
        // Vérification de l'ancien mot de passe
        $query = $pdo->prepare("SELECT PASSWORD FROM CLIENT WHERE IDCLIENT = :idClient");
        $query->execute([":idClient" => $id_client]);
        $currentPassword = $query->fetchColumn();

        // Vérifier si l'ancien mot de passe correspond
        if (password_verify($_POST["ancien-mdp"], $currentPassword)) {
            // Hasher le nouveau mot de passe
            $newPasswordHash = password_hash($_POST["nouveau-mdp"], PASSWORD_DEFAULT);
            
            // Mise à jour du mot de passe
            $query = $pdo->prepare("
                UPDATE CLIENT 
                SET PASSWORD = :newPassword
                WHERE IDCLIENT = :idClient
            ");

            $success = $query->execute([
                ":idClient" => $id_client,
                ":newPassword" => $newPasswordHash
            ]);

            // Redirection avec message approprié
            if ($success) {
                header("Location: compte.php?modif=ok&typeModif=2");
            } else {
                header("Location: compte.php?modif=erreur&typeModif=2");
            }
        } else {
            // L'ancien mot de passe ne correspond pas
            header("Location: compte.php?modif=erreur&typeModif=2&error=wrong_password");
        }
        exit();
    }else if ($_GET["typeModif"] == 3) {// Traitement de l'ajout d'adresse
        try {
            // Debug : Afficher les données reçues
            echo "Données reçues : <br>";
            echo "numRue: " . $_POST['numRue'] . "<br>";
            echo "nomRue: " . $_POST['nomRue'] . "<br>";
            echo "complement: " . $_POST['complement'] . "<br>";
            echo "ville: " . $_POST['ville'] . "<br>";
            echo "codePostal: " . $_POST['codePostal'] . "<br>";
            echo "pays: " . $_POST['pays'] . "<br>";

            // Validation des données
            $numRue = filter_var($_POST['numRue'], FILTER_VALIDATE_INT);
            $nomRue = trim($_POST['nomRue']);
            $complement = !empty($_POST['complement']) ? trim($_POST['complement']) : null;
            $ville = trim($_POST['ville']);
            $codePostal = filter_var($_POST['codePostal'], FILTER_VALIDATE_INT);
            $pays = trim($_POST['pays']);

            // Debug : Afficher les données après validation
            echo "<br>Après validation : <br>";
            echo "numRue: " . var_export($numRue, true) . "<br>";
            echo "nomRue: " . $nomRue . "<br>";
            echo "complement: " . $complement . "<br>";
            echo "ville: " . $ville . "<br>";
            echo "codePostal: " . var_export($codePostal, true) . "<br>";
            echo "pays: " . $pays . "<br>";

            // Vérification des longueurs et types
            if ($numRue === false || $numRue <= 0 || strlen($numRue) > 10) {
                throw new Exception("Numéro de rue invalide");
            }
            if (strlen($nomRue) > 60 || empty($nomRue)) {
                throw new Exception("Nom de rue invalide");
            }
            if ($complement !== null && strlen($complement) > 60) {
                throw new Exception("Complément d'adresse trop long");
            }
            if (strlen($ville) > 30 || empty($ville)) {
                throw new Exception("Nom de ville invalide");
            }
            if ($codePostal === false || $codePostal <= 0) {
                throw new Exception("Code postal invalide");
            }
            if (strlen($pays) > 30 || empty($pays)) {
                throw new Exception("Nom de pays invalide");
            }

            // Démarrer la transaction
            $pdo->beginTransaction();
            
            // Debug : Afficher la requête qui va être exécutée
            echo "<br>Requête d'insertion adresse : <br>";
            echo "INSERT INTO ADRESSE (NUMRUE, NOMRUE, COMPLEMENT, VILLE, CODEPOSTAL, PAYS) VALUES ";
            echo "($numRue, '$nomRue', " . ($complement ? "'$complement'" : "NULL") . ", '$ville', $codePostal, '$pays')";
            
            // Insertion de la nouvelle adresse
            $query = $pdo->prepare("
                INSERT INTO ADRESSE (NUMRUE, NOMRUE, COMPLEMENTADR, NOMVILLE, CODEPOSTAL, PAYS)
                VALUES (:numRue, :nomRue, :complement, :ville, :codePostal, :pays)
            ");
            
            $success = $query->execute([
                ':numRue' => $numRue,
                ':nomRue' => $nomRue,
                ':complement' => $complement,
                ':ville' => $ville,
                ':codePostal' => $codePostal,
                ':pays' => $pays
            ]);
            
            if (!$success) {
                echo "<br>Erreur lors de l'insertion de l'adresse : ";
                print_r($query->errorInfo());
                throw new Exception("Erreur lors de l'insertion de l'adresse");
            }
            
            // Récupérer l'ID de l'adresse nouvellement créée
            $idAdresse = $pdo->lastInsertId();
            echo "<br>ID de l'adresse créée : " . $idAdresse;
            
            // Debug : Afficher la requête de liaison
            echo "<br>Requête de liaison : <br>";
            echo "INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES ($id_client, $idAdresse)";
            
            // Lier l'adresse au client
            $query = $pdo->prepare("
                INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE)
                VALUES (:idClient, :idAdresse)
            ");
            
            $success = $query->execute([
                ':idClient' => $id_client,
                ':idAdresse' => $idAdresse
            ]);
            
            if (!$success) {
                echo "<br>Erreur lors de la liaison de l'adresse au client : ";
                print_r($query->errorInfo());
                throw new Exception("Erreur lors de la liaison de l'adresse au client");
            }
            
            // Si tout s'est bien passé, on valide la transaction
            $pdo->commit();
            echo "<br>Transaction validée avec succès !";
            
        } catch (Exception $e) {
            // En cas d'erreur, on annule la transaction
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
                echo "<br>Transaction annulée !";
            }
            echo "<br>Erreur : " . $e->getMessage();
            echo "<br>Trace : <pre>" . $e->getTraceAsString() . "</pre>";
        }
        exit();
    }
?>