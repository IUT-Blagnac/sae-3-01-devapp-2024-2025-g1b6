<?php
    session_start();
    include("connect.inc.php");

    //// Vérifier si l'utilisateur est un administrateur
    //if (!isset($_SESSION["admin"])) {
    //    header("Location: connexion.php");
    //    exit();
    //}

    if (isset($_GET['disconnect']) && $_GET['disconnect'] === 'true') {
        session_destroy();
        header("Location: connexion.php");
        exit();
    }

    header("Cache-Control: no-cache, must-revalidate");
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Css/all.css">
    <link rel="stylesheet" href="Css/dashboardAdmin.css">
    <title>Dashboard Administrateur</title>
</head>
<body>
    <?php include("header.php"); ?>

    <main>
        <div class="dashboard-container">
            <!-- Menu latéral -->
            <div class="sidebar">
                <nav class="sidebar-nav">
                    <ul class="nav-list">
                        <!-- Accueil -->
                        <li class="nav-item">
                            <div class="nav-link" data-section="accueil">
                                <i class="fas fa-home"></i>
                                <span>Accueil</span>
                            </div>
                        </li>

                        <!-- Produits -->
                        <li class="nav-item">
                            <div class="nav-link has-submenu">
                                <i class="fas fa-box"></i>
                                <span>Produits</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <ul class="submenu">
                                <li data-section="liste-produits">Liste des produits</li>
                                <li data-section="ajouter-produit">Ajouter un produit</li>
                            </ul>
                        </li>

                        <!-- Catégories -->
                        <li class="nav-item">
                            <div class="nav-link has-submenu">
                                <i class="fas fa-tags"></i>
                                <span>Catégories</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <ul class="submenu">
                                <li data-section="liste-categories">Liste des catégories</li>
                                <li data-section="ajouter-categorie">Ajouter une catégorie</li>
                            </ul>
                        </li>

                        <!-- Lots -->
                        <li class="nav-item">
                            <div class="nav-link has-submenu">
                                <i class="fas fa-layer-group"></i>
                                <span>Lots</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <ul class="submenu">
                                <li data-section="liste-lots">Liste des lots</li>
                                <li data-section="creer-lot">Créer un lot</li>
                            </ul>
                        </li>

                        <!-- Avis -->
                        <li class="nav-item">
                            <div class="nav-link has-submenu">
                                <i class="fas fa-comments"></i>
                                <span>Avis</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </div>
                            <ul class="submenu">
                                <li data-section="tous-les-avis">Tous les avis</li>
                                <li data-section="avis-signales">Avis signalés</li>
                                <li data-section="moderation">Modération</li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Contenu principal -->
            <div class="main-content">
                <!-- Le contenu sera chargé dynamiquement ici -->
                <div class="content-section" id="accueil">
                    <h1>Tableau de bord administrateur</h1>
                    
                    <!-- Statistiques générales -->
                    <div class="dashboard-stats">
                        <?php
                            // Nombre total de produits
                            $query = $pdo->query("SELECT COUNT(*) FROM PRODUIT");
                            $totalProduits = $query->fetchColumn();
                            
                            // Nombre total de clients
                            $query = $pdo->query("SELECT COUNT(*) FROM CLIENT");
                            $totalClients = $query->fetchColumn();
                            
                            // Nombre de commandes du mois
                            $query = $pdo->prepare("SELECT COUNT(*) FROM COMMANDE WHERE MONTH(DATECOMMANDE) = MONTH(CURRENT_DATE())");
                            $query->execute();
                            $commandesMois = $query->fetchColumn();
                            
                            // Chiffre d'affaires total
                            $query = $pdo->prepare("
                                SELECT SUM(P.PRIXHT * PA.QUANTITEPROD) 
                                FROM COMMANDE C 
                                JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE 
                                JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                                WHERE MONTH(C.DATECOMMANDE) = MONTH(CURRENT_DATE())
                            ");
                            $query->execute();
                            $caTotal = $query->fetchColumn() ?: 0;
                        ?>
                        
                        <div class="stat-card">
                            <h3>Produits</h3>
                            <p class="stat-number"><?= $totalProduits ?></p>
                            <p class="stat-label">Total des produits</p>
                        </div>
                        <div class="stat-card">
                            <h3>Clients</h3>
                            <p class="stat-number"><?= $totalClients ?></p>
                            <p class="stat-label">Clients inscrits</p>
                        </div>
                        <div class="stat-card">
                            <h3>Commandes</h3>
                            <p class="stat-number"><?= $commandesMois ?></p>
                            <p class="stat-label">Ce mois-ci</p>
                        </div>
                        <div class="stat-card">
                            <h3>CA Mensuel</h3>
                            <p class="stat-number"><?= number_format($caTotal, 2) ?> €</p>
                            <p class="stat-label">Ce mois-ci</p>
                        </div>
                    </div>

                    <!-- Graphiques -->
                    <div class="charts-container">
                        <!-- Graphique des ventes -->
                        <div class="chart-card">
                            <h3>Évolution des ventes</h3>
                            <canvas id="salesChart"></canvas>
                            <div class="period-selector">
                                <div class="slider"></div>
                                <button class="period-btn active" data-period="day" data-chart="sales">Jour</button>
                                <button class="period-btn" data-period="month" data-chart="sales">Mois</button>
                                <button class="period-btn" data-period="year" data-chart="sales">Année</button>
                            </div>
                        </div>
                        
                        <!-- Graphique des nouveaux clients -->
                        <div class="chart-card">
                            <h3>Nouveaux clients</h3>
                            <canvas id="newClientsChart"></canvas>
                            <div class="period-selector">
                                <div class="slider"></div>
                                <button class="period-btn active" data-period="day" data-chart="clients">Jour</button>
                                <button class="period-btn" data-period="month" data-chart="clients">Mois</button>
                                <button class="period-btn" data-period="year" data-chart="clients">Année</button>
                            </div>
                        </div>
                        
                        <!-- Top produits -->
                        <div class="chart-card">
                            <h3>Top 5 des produits</h3>
                            <canvas id="topProductsChart"></canvas>
                        </div>
                        
                        <!-- Répartition des commandes -->
                        <div class="chart-card">
                            <h3>Statuts des commandes</h3>
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>

                    <?php
                        // Données pour les graphiques
                        // Ventes des 6 derniers mois
                        $query = $pdo->prepare("
                            WITH RECURSIVE dates AS (
                                SELECT CURDATE() - INTERVAL 29 DAY as date
                                UNION ALL
                                SELECT date + INTERVAL 1 DAY
                                FROM dates
                                WHERE date < CURDATE()
                            )
                            SELECT 
                                DATE_FORMAT(d.date, '%e %b') as mois,
                                COALESCE(SUM(P.PRIXHT * PA.QUANTITEPROD), 0) as total_ventes
                            FROM dates d
                            LEFT JOIN COMMANDE C ON DATE(C.DATECOMMANDE) = d.date
                            LEFT JOIN PANIER PA ON C.NUMCOMMANDE = PA.IDCOMMANDE
                            LEFT JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                            GROUP BY d.date
                            ORDER BY d.date ASC
                        ");
                        $query->execute();
                        $ventesData = $query->fetchAll();

                        // Nouveaux clients par mois
                        $query = $pdo->prepare("
                            WITH RECURSIVE dates AS (
                                SELECT CURDATE() - INTERVAL 29 DAY as date
                                UNION ALL
                                SELECT date + INTERVAL 1 DAY
                                FROM dates
                                WHERE date < CURDATE()
                            )
                            SELECT 
                                DATE_FORMAT(d.date, '%e %b') as mois,
                                COUNT(C.IDCLIENT) as nouveaux_clients
                            FROM dates d
                            LEFT JOIN CLIENT C ON DATE(C.DATEINSCRIPTION) = d.date
                            GROUP BY d.date
                            ORDER BY d.date ASC
                        ");
                        $query->execute();
                        $clientsData = $query->fetchAll();

                        // Top 5 des produits (sur les 30 derniers jours)
                        $query = $pdo->prepare("
                            SELECT 
                                P.NOMPROD,
                                SUM(PA.QUANTITEPROD) as quantite_vendue
                            FROM PANIER PA
                            JOIN PRODUIT P ON PA.IDPROD = P.IDPROD
                            JOIN COMMANDE C ON PA.IDCOMMANDE = C.NUMCOMMANDE
                            WHERE PA.IDCOMMANDE != 0
                            AND C.DATECOMMANDE >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
                            GROUP BY P.IDPROD, P.NOMPROD
                            ORDER BY quantite_vendue DESC
                            LIMIT 5
                        ");
                        $query->execute();
                        $topProduits = $query->fetchAll();

                        // Statuts des commandes (sur les 30 derniers jours)
                        $query = $pdo->prepare("
                            SELECT 
                                STATUTLIVRAISON,
                                COUNT(*) as nombre
                            FROM COMMANDE
                            WHERE DATECOMMANDE >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
                            GROUP BY STATUTLIVRAISON
                        ");
                        $query->execute();
                        $statutsCommandes = $query->fetchAll();
                    ?>
                </div>
            </div>
        </div>

        <button class="disconnect-btn">Se déconnecter</button>
    </main>

    <?php include("footer.php"); ?>

    <!-- Ajouter Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du menu déroulant
            const menuItems = document.querySelectorAll('.nav-link.has-submenu');
            
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Toggle active class
                    this.classList.toggle('active');
                    
                    // Toggle submenu visibility
                    const submenu = this.nextElementSibling;
                    if (submenu.style.maxHeight) {
                        submenu.style.maxHeight = null;
                    } else {
                        submenu.style.maxHeight = submenu.scrollHeight + "px";
                    }
                    
                    // Rotate arrow
                    const arrow = this.querySelector('.arrow');
                    arrow.style.transform = arrow.style.transform === 'rotate(180deg)' ? 
                                          'rotate(0deg)' : 'rotate(180deg)';
                });
            });

            // Gestion de la déconnexion
            document.querySelector('.disconnect-btn').addEventListener('click', () => {
                window.location.href = 'dashboardAdmin.php?disconnect=true';
            });

            // Gestion des boutons de période et de l'effet de glissement
            document.querySelectorAll('.period-selector').forEach(selector => {
                const buttons = selector.querySelectorAll('.period-btn');
                const slider = selector.querySelector('.slider');
                
                buttons.forEach((btn, index) => {
                    btn.addEventListener('click', async function() {
                        const period = this.dataset.period;
                        const chartType = this.dataset.chart;
                        
                        // Mise à jour du slider
                        slider.style.transform = `translateX(${index * 100}%)`;
                        
                        // Mise à jour des classes active
                        buttons.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                    });
                });

                // Positionner initialement le slider
                const activeButton = selector.querySelector('.period-btn.active');
                const activeIndex = Array.from(buttons).indexOf(activeButton);
                slider.style.transform = `translateX(${activeIndex * 100}%)`;
            });

            let salesChart, clientsChart;
            
            // Données PHP vers JavaScript
            const ventesData = <?= json_encode($ventesData) ?>;
            const clientsData = <?= json_encode($clientsData) ?>;
            const topProduits = <?= json_encode($topProduits) ?>;
            const statutsCommandes = <?= json_encode($statutsCommandes) ?>;

            // Fonction pour charger les données selon la période
            async function loadChartData(chartType, period) {
                try {
                    const response = await fetch(`getChartData.php?type=${chartType}&period=${period}`);
                    const data = await response.json();
                    
                    if (!response.ok) {
                        console.error('Erreur serveur:', data);
                        throw new Error(`Erreur ${response.status}: ${data.error}\nLigne: ${data.line}\nFichier: ${data.file}\nTrace: ${data.trace}`);
                    }
                    
                    return data;
                } catch (error) {
                    console.error('Erreur détaillée:', error);
                    return null;
                }
            }

            // Graphique des ventes
            salesChart = new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: ventesData.map(item => item.mois),
                    datasets: [{
                        label: 'Ventes (€)',
                        data: ventesData.map(item => item.total_ventes),
                        borderColor: '#6d00b0',
                        tension: 0.3,
                        fill: true,
                        backgroundColor: 'rgba(109, 0, 176, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' €';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' €';
                                }
                            }
                        }
                    }
                }
            });

            // Fonction pour mettre à jour les graphiques
            async function updateChart(chartType, period, chart) {
                const data = await loadChartData(chartType, period);
                if (data) {
                    chart.data.labels = data.labels;
                    chart.data.datasets[0].data = data.values;
                    
                    // Ajuster les options en fonction de la période
                    if (chartType === 'sales') {
                        chart.options.scales.x.time = {
                            unit: period === 'day' ? 'day' : period === 'month' ? 'month' : 'year'
                        };
                    }
                    
                    chart.update();
                }
            }

            // Gestion des boutons de période
            document.querySelectorAll('.period-selector').forEach(selector => {
                const buttons = selector.querySelectorAll('.period-btn');
                const slider = selector.querySelector('.slider');
                
                buttons.forEach((btn, index) => {
                    btn.addEventListener('click', async function() {
                        const period = this.dataset.period;
                        const chartType = this.dataset.chart;
                        
                        // Mise à jour du slider
                        slider.style.transform = `translateX(${index * 100}%)`;
                        
                        // Mise à jour des classes active
                        buttons.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Mise à jour du graphique
                        const chart = chartType === 'sales' ? salesChart : clientsChart;
                        await updateChart(chartType, period, chart);
                    });
                });

                // Positionner initialement le slider
                const activeButton = selector.querySelector('.period-btn.active');
                const activeIndex = Array.from(buttons).indexOf(activeButton);
                slider.style.transform = `translateX(${activeIndex * 100}%)`;
            });

            // Graphique des nouveaux clients
            clientsChart = new Chart(document.getElementById('newClientsChart'), {
                type: 'bar',
                data: {
                    labels: clientsData.map(item => item.mois),
                    datasets: [{
                        label: 'Nouveaux clients',
                        data: clientsData.map(item => item.nouveaux_clients),
                        backgroundColor: '#8A2BE2'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Graphique des top produits
            new Chart(document.getElementById('topProductsChart'), {
                type: 'doughnut',
                data: {
                    labels: topProduits.map(item => item.NOMPROD),
                    datasets: [{
                        data: topProduits.map(item => item.quantite_vendue),
                        backgroundColor: [
                            '#6d00b0',
                            '#8A2BE2',
                            '#9370DB',
                            '#BA55D3',
                            '#DDA0DD'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: '30 derniers jours'
                        }
                    }
                }
            });

            // Graphique des statuts de commande
            new Chart(document.getElementById('orderStatusChart'), {
                type: 'pie',
                data: {
                    labels: statutsCommandes.map(item => item.STATUTLIVRAISON),
                    datasets: [{
                        data: statutsCommandes.map(item => item.nombre),
                        backgroundColor: [
                            '#6d00b0',
                            '#8A2BE2',
                            '#9370DB',
                            '#BA55D3'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: '30 derniers jours'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html> 