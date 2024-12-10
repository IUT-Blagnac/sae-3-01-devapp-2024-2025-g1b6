<?php 
//rechercheAvancee.php
function rechercheAvancee($criteres, $pdo) {
    $query = "SELECT p.IDPROD, p.NOMPROD, p.DESCPROD, p.PRIXHT, p.QTESTOCK, c.NOMCATEG 
          FROM PRODUIT p
          LEFT JOIN APPARTENIRCATEG ap on ap.IDPROD = p.IDPROD
          LEFT JOIN CATEGORIE c ON ap.IDCATEG = c.IDCATEG
          WHERE 1=1";

    $params = [];

    // Mot clé
    if (!empty($criteres['mot_cle'])) {
        $query .= " AND (LOWER(p.NOMPROD) LIKE :mot_cle OR LOWER(p.DESCPROD) LIKE :mot_cle)";
        $params[':mot_cle'] = "%" . strtolower($criteres['mot_cle']) . "%";
    }

    // Catégorie
    if (!empty($criteres['categorie'])) {
        $query .= " AND LOWER(c.NOMCATEG) = :categorie";
        $params[':categorie'] = strtolower($criteres['categorie']);
    }

    // Marque
    if (!empty($criteres['marque'])) {
        $query .= " AND LOWER(p.NOMMARQUE) = :marque";
        $params[':marque'] = strtolower($criteres['marque']);
    }

    // Prix min et max
    if (!empty($criteres['prix_min'])) {
        $query .= " AND p.PRIXHT >= :prix_min";
        $params[':prix_min'] = $criteres['prix_min'];
    }
    if (!empty($criteres['prix_max'])) {
        $query .= " AND p.PRIXHT <= :prix_max";
        $params[':prix_max'] = $criteres['prix_max'];
    }

    // En stock
    if ($criteres['en_stock']) {
        $query .= " AND p.QTESTOCK > 0";
    }

    // Exécuter la requête
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php
// recherche.php
require_once 'rechercheAvancee.php'; // Inclure la fonction de recherche
require_once 'connect.inc.php'; // Connexion PDO

// Récupérer les termes de recherche depuis la barre de recherche
$criteres = [
    'mot_cle' => $_GET['mot_cle'] ?? '', // Récupérer la valeur saisie par l'utilisateur
    'categorie' => $_GET['categorie'] ?? '', // Récupérer la catégorie choisie par l'utilisateur
    'marque' => $_GET['marque'] ?? '', // Récupérer la marque choisie
    'prix_min' => $_GET['prix_min'] ?? '', // Récupérer le prix minimum
    'prix_max' => $_GET['prix_max'] ?? '', // Récupérer le prix maximum
    'en_stock' => isset($_GET['en_stock']) ? 1 : 0 // Vérifier si l'option "en stock" est cochée
];

// Charger les catégories dynamiquement (facultatif)
$stmt = $pdo->query("SELECT NOMCATEG FROM CATEGORIE");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Charger les marques dynamiquement (facultatif)
$stmt = $pdo->query("SELECT NOMMARQUE FROM MARQUE");
$marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Appeler la fonction de recherche avancée avec le tableau des critères et l'objet PDO
$resultats = rechercheAvancee($criteres, $pdo);
?>