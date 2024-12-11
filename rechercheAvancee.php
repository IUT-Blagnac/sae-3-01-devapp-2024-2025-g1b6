<?php
// rechercheAvancee.php
function rechercheAvancee($criteres, $pdo) {
    // Début de la construction de la requête
    $query = "SELECT DISTINCT p.IDPROD, p.NOMPROD, p.DESCPROD, p.PRIXHT, p.QTESTOCK, m.NOMMARQUE
              FROM PRODUIT p
              LEFT JOIN MARQUE m ON p.IDMARQUE = m.IDMARQUE
              LEFT JOIN APPARTENIRCATEG ap on ap.IDPROD = p.IDPROD
              LEFT JOIN CATEGORIE c ON ap.IDCATEG = c.IDCATEG
              WHERE 1=1";


    $conditions = [];
    $params = [];


    // Mot-clé
    if (!empty($criteres['mot_cle'])) {
        $conditions[] = "(p.NOMPROD LIKE :mot_cle OR p.DESCPROD LIKE :mot_cle)";
        $params[':mot_cle'] = '%' . $criteres['mot_cle'] . '%';
    }


    // Catégorie
    if (!empty($criteres['categorie'])) {
        $conditions[] = "ap.IDCATEG = :categorie";
        $params[':categorie'] = $criteres['categorie'];
    }


    // Marque
    if (!empty($criteres['marque'])) {
        $conditions[] = "m.NOMMARQUE = :marque"; // Modification ici, rechercher sur m.NOMMARQUE
        $params[':marque'] = $criteres['marque'];
    }


    // Prix minimum
    if (!empty($criteres['prix_min'])) {
        $conditions[] = "p.PRIXHT >= :prix_min";
        $params[':prix_min'] = $criteres['prix_min'];
    }


    // Prix maximum
    if (!empty($criteres['prix_max'])) {
        $conditions[] = "p.PRIXHT <= :prix_max";
        $params[':prix_max'] = $criteres['prix_max'];
    }


    // En stock
    if (!empty($criteres['en_stock'])) {
        $conditions[] = "p.QTESTOCK > 0";
    }


    // Ajout des conditions à la requête
    if (!empty($conditions)) {
        $query .= " AND " . implode(' AND ', $conditions);
    }


    // Préparation et exécution de la requête
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $produits;
    } catch (PDOException $e) {
        // Gestion des erreurs PDO
        echo "<h4>Erreur SQL :</h4><pre>" . $e->getMessage() . "</pre>";
        return [];
    }
}
?>
