<?php
session_start();
include("connect.inc.php");

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$period = $_GET['period'] ?? 'day';

$response = ['labels' => [], 'values' => []];

try {
    switch ($type) {
        case 'sales':
            // Requête pour les ventes
            switch ($period) {
                case 'day':
                    // Ventes par jour sur les 30 derniers jours
                    $query = $pdo->prepare("
                        WITH RECURSIVE dates AS (
                            SELECT CURDATE() - INTERVAL 29 DAY as date
                            UNION ALL
                            SELECT date + INTERVAL 1 DAY
                            FROM dates
                            WHERE date < CURDATE()
                        )
                        SELECT 
                            DATE_FORMAT(d.date, '%e %b') as period,
                            COALESCE(SUM(P.PRIXHT * PA.QUANTITEPROD), 0) as total
                        FROM dates d
                        LEFT JOIN COMMANDE C ON DATE(C.DATECOMMANDE) = d.date
                        LEFT JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                        LEFT JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                        GROUP BY d.date, DATE_FORMAT(d.date, '%e %b')
                        ORDER BY d.date
                    ");
                    break;

                case 'month':
                    // Ventes par mois sur les 12 derniers mois
                    $query = $pdo->prepare("
                        WITH RECURSIVE months AS (
                            SELECT DATE_FORMAT(CURDATE() - INTERVAL 11 MONTH, '%Y-%m-01') as month
                            UNION ALL
                            SELECT DATE_ADD(month, INTERVAL 1 MONTH)
                            FROM months
                            WHERE month < DATE_FORMAT(CURDATE(), '%Y-%m-01')
                        )
                        SELECT 
                            DATE_FORMAT(m.month, '%b %Y') as period,
                            COALESCE(SUM(P.PRIXHT * PA.QUANTITEPROD), 0) as total
                        FROM months m
                        LEFT JOIN COMMANDE C ON DATE_FORMAT(C.DATECOMMANDE, '%Y-%m') = DATE_FORMAT(m.month, '%Y-%m')
                        LEFT JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                        LEFT JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                        GROUP BY m.month, DATE_FORMAT(m.month, '%b %Y')
                        ORDER BY m.month
                    ");
                    break;

                case 'year':
                    // Ventes par année sur les 5 dernières années
                    $query = $pdo->prepare("
                        WITH RECURSIVE years AS (
                            SELECT YEAR(CURDATE()) - 4 as year
                            UNION ALL
                            SELECT year + 1
                            FROM years
                            WHERE year < YEAR(CURDATE())
                        )
                        SELECT 
                            CAST(y.year AS CHAR) as period,
                            COALESCE(SUM(P.PRIXHT * PA.QUANTITEPROD), 0) as total
                        FROM years y
                        LEFT JOIN COMMANDE C ON YEAR(C.DATECOMMANDE) = y.year
                        LEFT JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                        LEFT JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                        GROUP BY y.year
                        ORDER BY y.year
                    ");
                    break;
            }
            break;

        case 'clients':
            // Requête pour les nouveaux clients
            switch ($period) {
                case 'day':
                    // Nouveaux clients par jour sur les 30 derniers jours
                    $query = $pdo->prepare("
                        WITH RECURSIVE dates AS (
                            SELECT CURDATE() - INTERVAL 29 DAY as date
                            UNION ALL
                            SELECT date + INTERVAL 1 DAY
                            FROM dates
                            WHERE date < CURDATE()
                        )
                        SELECT 
                            DATE_FORMAT(d.date, '%e %b') as period,
                            COUNT(C.IDCLIENT) as total
                        FROM dates d
                        LEFT JOIN CLIENT C ON DATE(C.DATEINSCRIPTION) = d.date
                        GROUP BY d.date, DATE_FORMAT(d.date, '%e %b')
                        ORDER BY d.date
                    ");
                    break;

                case 'month':
                    // Nouveaux clients par mois sur les 12 derniers mois
                    $query = $pdo->prepare("
                        WITH RECURSIVE months AS (
                            SELECT DATE_FORMAT(CURDATE() - INTERVAL 11 MONTH, '%Y-%m-01') as month
                            UNION ALL
                            SELECT DATE_ADD(month, INTERVAL 1 MONTH)
                            FROM months
                            WHERE month < DATE_FORMAT(CURDATE(), '%Y-%m-01')
                        )
                        SELECT 
                            DATE_FORMAT(m.month, '%b %Y') as period,
                            COUNT(C.IDCLIENT) as total
                        FROM months m
                        LEFT JOIN CLIENT C ON DATE_FORMAT(C.DATEINSCRIPTION, '%Y-%m') = DATE_FORMAT(m.month, '%Y-%m')
                        GROUP BY m.month, DATE_FORMAT(m.month, '%b %Y')
                        ORDER BY m.month
                    ");
                    break;

                case 'year':
                    // Nouveaux clients par année sur les 5 dernières années
                    $query = $pdo->prepare("
                        WITH RECURSIVE years AS (
                            SELECT YEAR(CURDATE()) - 4 as year
                            UNION ALL
                            SELECT year + 1
                            FROM years
                            WHERE year < YEAR(CURDATE())
                        )
                        SELECT 
                            CAST(y.year AS CHAR) as period,
                            COUNT(C.IDCLIENT) as total
                        FROM years y
                        LEFT JOIN CLIENT C ON YEAR(C.DATEINSCRIPTION) = y.year
                        GROUP BY y.year
                        ORDER BY y.year
                    ");
                    break;
            }
            break;

            
        case 'top_products':
            // Requête pour le top 5 des produits
            switch ($period) {
                case 'day':
                    // Top 5 des produits du jour
                    $query = $pdo->prepare("
                        SELECT 
                            P.NOMPROD as period,
                            SUM(PA.QUANTITEPROD) as total
                        FROM COMMANDE C
                        JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                        JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                        WHERE DATE(C.DATECOMMANDE) = CURDATE()
                        GROUP BY P.IDPROD, P.NOMPROD
                        ORDER BY total DESC
                        LIMIT 5
                    ");
                    break;

                case 'month':
                    // Top 5 des produits du mois en cours
                    $query = $pdo->prepare("
                        SELECT 
                            P.NOMPROD as period,
                            COALESCE(SUM(PA.QUANTITEPROD), 0) as total
                        FROM COMMANDE C
                        JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                        JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                        WHERE YEAR(C.DATECOMMANDE) = YEAR(CURDATE())
                        AND MONTH(C.DATECOMMANDE) = MONTH(CURDATE())
                        GROUP BY P.IDPROD, P.NOMPROD
                        ORDER BY total DESC
                        LIMIT 5
                    ");
                    break;

                case 'year':
                    // Top 5 des produits des 12 derniers mois
                    $query = $pdo->prepare("
                        SELECT 
                            P.NOMPROD as period,
                            COALESCE(SUM(PA.QUANTITEPROD), 0) as total
                        FROM PRODUIT P
                        LEFT JOIN PANIER PA ON P.IDPROD = PA.IDPROD
                        LEFT JOIN COMMANDE C ON PA.IDCOMMANDE = C.NUMCOMMANDE 
                            AND C.DATECOMMANDE >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                        GROUP BY P.IDPROD, P.NOMPROD
                        HAVING total > 0
                        ORDER BY total DESC
                        LIMIT 5 
                    ");
                    break;
            }
            break;

        case 'order_status':
            // Statuts des commandes sur les 30 derniers jours
            $query = $pdo->prepare("
                SELECT 
                    STATUTLIVRAISON as period,
                    COUNT(*) as total
                FROM COMMANDE
                WHERE DATECOMMANDE >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY STATUTLIVRAISON
            ");
            break;

        default:
            throw new Exception('Type de graphique non reconnu');
    }

    if (isset($query)) {
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            $response['labels'][] = $row['period'];
            $response['values'][] = floatval($row['total']);
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 