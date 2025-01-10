<?php
    session_start();
    include("connect.inc.php");
    
    if (!isset($_SESSION["admin"])) {
        header("Location: connexion.php");
        exit();
    }
    
    // Vérification de la déconnexion
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Tableau de bord Administrateur</title>
</head>
<body>
    <?php include("header.php"); ?>
 
    <main> 
        <div class="dashboard-container">
            <?php 
            $currentPage = 'accueil';
            include("includes/adminSidebar.php"); 
            ?>

            <!-- Contenu principal -->
            <div class="main-content">
                <div class="content-section" id="accueil">
                    <h1>Tableau de bord administrateur</h1>
                    
                    <!-- Statistiques générales -->
                    <div class="dashboard-stats">
                        <?php
                            // Nombre total de produits
                            $query = $pdo->prepare("SELECT COUNT(*) FROM PRODUIT");
                            $query->execute();
                            $totalProduits = $query->fetchColumn();
                            
                            // Nombre total de clients
                            $query = $pdo->prepare("SELECT COUNT(*) FROM CLIENT");
                            $query->execute(); 
                            $totalClients = $query->fetchColumn();
                            
                            // Nombre de commandes du mois
                            $query = $pdo->prepare("SELECT COUNT(*) FROM COMMANDE WHERE MONTH(DATECOMMANDE) = MONTH(CURRENT_DATE())");
                            $query->execute();
                            $commandesMois = $query->fetchColumn();
                            
                            // Chiffre d'affaires du mois
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
                            <div class="period-selector">
                                <div class="slider"></div>
                                <button class="period-btn active" data-period="day" data-chart="top_products">Jour</button>
                                <button class="period-btn" data-period="month" data-chart="top_products">Mois</button>
                                <button class="period-btn" data-period="year" data-chart="top_products">Année</button>
                            </div>
                        </div>
                        
                        <!-- Statuts des commandes -->
                        <div class="chart-card">
                            <h3>Statuts des commandes</h3>
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="disconnect-btn">Se déconnecter</button>
    </main>

    <?php include("footer.php"); ?>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour charger les données selon la période
            async function loadChartData(chartType, period) {
                try {
                    console.log(`Chargement des données pour ${chartType} (${period})`);
                    const response = await fetch(`getChartData.php?type=${chartType}&period=${period}`);
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.error || 'Une erreur est survenue');
                    }
                    
                    console.log('Données reçues:', data);
                    return data;
                } catch (error) { 
                    console.error('Erreur lors du chargement des données:', error); 
                    alert(`Erreur lors du chargement des données: ${error.message}`);
                    return null;
                }
            }

            // Fonction pour mettre à jour les graphiques
            async function updateChart(chartType, period, chart) {
                const data = await loadChartData(chartType, period);
                if (!data) return;

                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.values;

                if (chartType === 'top_products') {
                    if (data.labels.length === 0) {
                        chart.data.labels = ['Aucune commande'];
                        chart.data.datasets[0].data = [1];
                        chart.data.datasets[0].backgroundColor = ['#e0e0e0'];
                    } else {
                        chart.data.datasets[0].backgroundColor = [
                            '#6d00b0',
                            '#8A2BE2',
                            '#9370DB',
                            '#BA55D3',
                            '#DDA0DD'
                        ];
                    }
                    
                    let periodText = period === 'day' ? "Aujourd'hui" : 
                                   period === 'month' ? "Ce mois" : "Cette année";
                    chart.options.plugins.title.text = `Top 5 des produits - ${periodText}`;
                }

                chart.update();
            }

            // Gestion du menu déroulant
            const menuItems = document.querySelectorAll('.nav-link.has-submenu');
            
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.classList.toggle('active');
                    
                    const submenu = this.nextElementSibling;
                    if (submenu.style.maxHeight) {
                        submenu.style.maxHeight = null;
                    } else {
                        submenu.style.maxHeight = submenu.scrollHeight + "px";
                    }
                    
                    const arrow = this.querySelector('.arrow');
                    arrow.style.transform = arrow.style.transform === 'rotate(180deg)' ? 
                                          'rotate(0deg)' : 'rotate(180deg)';
                });
            });

            // Gestion de la déconnexion
            document.querySelector('.disconnect-btn').addEventListener('click', () => {
                window.location.href = 'dashboardAdmin.php?disconnect=true';
            });

            let salesChart, clientsChart, topProductsChart, orderStatusChart;
            
            // Initialisation des graphiques
            salesChart = new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Ventes (€)',
                        data: [],
                        borderColor: '#6d00b0',
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(109, 0, 176, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            clientsChart = new Chart(document.getElementById('newClientsChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Nouveaux clients',
                        data: [],
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
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });

            topProductsChart = new Chart(document.getElementById('topProductsChart'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#6d00b0',
                            '#8A2BE2',
                            '#9370DB',
                            '#BA55D3',
                            '#DDA0DD'
                        ],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            display: true
                        },
                        title: {
                            display: true,
                            text: 'Top 5 des produits',
                            font: {
                                size: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} commandes`;
                                }
                            }
                        }
                    }
                }
            });

            orderStatusChart = new Chart(document.getElementById('orderStatusChart'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#6d00b0',
                            '#BA55D3',
                            '#DDA0DD'
                        ],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            display: true
                        },
                        title: {
                            display: true,
                            text: 'Statuts des commandes (30 derniers jours)',
                            font: {
                                size: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} commandes`;
                                }
                            }
                        }
                    }
                }
            });

            // Chargement initial des données
            updateChart('sales', 'day', salesChart);
            updateChart('clients', 'day', clientsChart);
            updateChart('top_products', 'day', topProductsChart);
            loadChartData('order_status').then(data => {
                if (data && data.labels.length > 0) {
                    orderStatusChart.data.labels = data.labels;
                    orderStatusChart.data.datasets[0].data = data.values;
                    orderStatusChart.update();
                }
            });

            // Gestion des boutons de période
            document.querySelectorAll('.period-selector').forEach(selector => {
                const buttons = selector.querySelectorAll('.period-btn');
                const slider = selector.querySelector('.slider');
                
                buttons.forEach((btn, index) => {
                    btn.addEventListener('click', async function() {
                        console.log('Bouton cliqué:', this.dataset.period, this.dataset.chart); // Debug
                        const period = this.dataset.period;
                        const chartType = this.dataset.chart;
                        
                        slider.style.transform = `translateX(${index * 100}%)`;
                        buttons.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        let chart;
                        switch(chartType) {
                            case 'sales':
                                chart = salesChart;
                                break;
                            case 'clients':
                                chart = clientsChart;
                                break;
                            case 'top_products':
                                console.log('Mise à jour du top 5 des produits'); // Debug
                                chart = topProductsChart;
                                break;
                        }
                        if (chart) {
                            await updateChart(chartType, period, chart);
                        }
                    });
                });

                // Position initiale du slider
                const activeButton = selector.querySelector('.period-btn.active');
                const activeIndex = Array.from(buttons).indexOf(activeButton);
                slider.style.transform = `translateX(${activeIndex * 100}%)`;
            });
        });
    </script>
</body>
</html>
