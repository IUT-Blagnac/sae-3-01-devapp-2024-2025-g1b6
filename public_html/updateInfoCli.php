<?php 
    session_start();
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION["user"]["IDCLIENT"])) {
        header("Location: connexion.php");
        exit();
    }elseif (!isset($_GET["typeModif"])  || empty($_GET["typeModif"]) ||!($_GET["typeModif"] >= 1 && $_GET["typeModif"] <= 5) || empty($_POST)) {
        header("Location: compte.php");
        exit();
    }

    // Récupérer l'ID client depuis la session
    $id_client = $_SESSION["user"]["IDCLIENT"];

    if ($_GET["typeModif"] == 1 ){
        var_dump($_POST);

    }
?>