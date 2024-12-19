<?php
session_start();
include("connect.inc.php");

if (!isset($_GET['type']) || !isset($_GET['period'])) {
    exit(json_encode(['error' => 'Paramètres manquants']));
}

$type = $_GET['type'];
$period = $_GET['period'];

try {
    $data = [];
    
    if ($type === 'sales') {
        if ($period === 'day') {
            $query = $pdo->prepare("
                WITH RECURSIVE dates AS (
                    SELECT CURDATE() - INTERVAL 29 DAY as date
                    UNION ALL
                    SELECT date + INTERVAL 1 DAY
                    FROM dates
                    WHERE date < CURDATE()
                )
                SELECT 
                    DATE_FORMAT(d.date, '%e %b') as periode,
                    COALESCE(SUM(P.PRIXHT * PA.QUANTITEPROD), 0) as total_ventes
                FROM dates d
                LEFT JOIN COMMANDE C ON DATE(C.DATECOMMANDE) = d.date
                LEFT JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                LEFT JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                GROUP BY d.date
                ORDER BY d.date ASC
            ");
            $query->execute();
        } 
        elseif ($period === 'month') {
            $query = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(DATECOMMANDE, '%b %Y') as periode,
                    SUM(P.PRIXHT * PA.QUANTITEPROD) as total_ventes
                FROM COMMANDE C
                JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                WHERE DATECOMMANDE >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
                GROUP BY YEAR(DATECOMMANDE), MONTH(DATECOMMANDE)
                ORDER BY YEAR(DATECOMMANDE), MONTH(DATECOMMANDE)
            ");
            $query->execute();
        }
        else { // year
            $query = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(DATECOMMANDE, '%Y') as periode,
                    SUM(P.PRIXHT * PA.QUANTITEPROD) as total_ventes
                FROM COMMANDE C
                JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                WHERE DATECOMMANDE >= DATE_SUB(CURRENT_DATE(), INTERVAL 5 YEAR)
                GROUP BY YEAR(DATECOMMANDE)
                ORDER BY YEAR(DATECOMMANDE)
            ");
            $query->execute();
        }
        
        $results = $query->fetchAll();
        $data = [
            'labels' => array_column($results, 'periode'),
            'values' => array_column($results, 'total_ventes')
        ];
    } 
    elseif ($type === 'clients') {
        if ($period === 'day') {
            $query = $pdo->prepare("
                WITH RECURSIVE dates AS (
                    SELECT CURDATE() - INTERVAL 29 DAY as date
                    UNION ALL
                    SELECT date + INTERVAL 1 DAY
                    FROM dates
                    WHERE date < CURDATE()
                )
                SELECT 
                    DATE_FORMAT(d.date, '%e %b') as periode,
                    COUNT(C.IDCLIENT) as nouveaux_clients
                FROM dates d
                LEFT JOIN CLIENT C ON DATE(C.DATEINSCRIPTION) = d.date
                GROUP BY d.date
                ORDER BY d.date ASC
            ");
            $query->execute();
        }
        elseif ($period === 'month') {
            $query = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(DATEINSCRIPTION, '%b %Y') as periode,
                    COUNT(*) as nouveaux_clients
                FROM CLIENT
                WHERE DATEINSCRIPTION >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
                GROUP BY YEAR(DATEINSCRIPTION), MONTH(DATEINSCRIPTION)
                ORDER BY YEAR(DATEINSCRIPTION), MONTH(DATEINSCRIPTION)
            ");
            $query->execute();
        }
        else { // year
            $query = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(DATEINSCRIPTION, '%Y') as periode,
                    COUNT(*) as nouveaux_clients
                FROM CLIENT
                WHERE DATEINSCRIPTION >= DATE_SUB(CURRENT_DATE(), INTERVAL 5 YEAR)
                GROUP BY YEAR(DATEINSCRIPTION)
                ORDER BY YEAR(DATEINSCRIPTION)
            ");
            $query->execute();
        }
        
        $results = $query->fetchAll();
        $data = [
            'labels' => array_column($results, 'periode'),
            'values' => array_column($results, 'nouveaux_clients')
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
} 