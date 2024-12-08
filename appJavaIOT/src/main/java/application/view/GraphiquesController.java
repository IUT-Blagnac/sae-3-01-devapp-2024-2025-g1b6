package application.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.chart.*;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Tab;
import javafx.scene.control.TabPane;
import application.model.Measure;
import application.model.Room;
import application.model.SyncData;

import java.io.IOException;
import java.nio.file.*;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

/**
 * Contrôleur pour gérer l'affichage des graphiques de données pour les salles et les panneaux solaires.
 * <p>
 * Cette classe est responsable de l'affichage des données sous forme de graphiques interactifs (barres et courbes) dans une interface JavaFX.
 * Elle permet de surveiller les alertes et les nouveaux fichiers de données via un mécanisme de surveillance de fichiers.
 * </p>
 * 
 * @author Marwane Ibrahim
 */
public class GraphiquesController {

    private Map<String, Map<String, Double>> sensorData = new HashMap<>();
    private ExecutorService alertExecutor;
    private ExecutorService dataExecutor;
    private volatile boolean running = true;

    @FXML
    private TabPane tabPane;

    @FXML
    private Button buttonRetour;

    /**
     * Initialise le contrôleur, charge les données des salles et crée les onglets de graphiques.
     * 
     * Cette méthode est appelée lors de l'initialisation du contrôleur. Elle charge les données des salles et des panneaux solaires via le singleton {@link SyncData}.
     * Elle crée ensuite des onglets de graphiques pour chaque type de donnée et lance des threads pour surveiller les alertes et les fichiers de données.
     */
    public void initialize() {
        try {
            SyncData syncData = SyncData.getInstance();
            syncData.fillRoomList();  // Charger les données des salles et panneaux solaires

            // Créer les onglets pour chaque type de donnée
            for (Room room : syncData.getRoomsMap().values()) {
                for (Measure measure : room.getRoomValues()) {
                    for (Map.Entry<String, Object> entry : measure.getValues().entrySet()) {
                        String key = entry.getKey();
                        double value = ((Number) entry.getValue()).doubleValue();
                        sensorData.computeIfAbsent(key, k -> new HashMap<>()).put(room.getRoomName(), value);
                    }
                }
            }

            // Crée un onglet pour chaque type de donnée
            for (String key : sensorData.keySet()) {
                Tab tab = new Tab(key);
                tab.setContent(createChart(key, sensorData.get(key)));
                tabPane.getTabs().add(tab);
            }

            // Lancer les threads pour surveiller les alertes et les nouveaux fichiers JSON
            startAlertMonitoring(Paths.get("src/main/resources/application/data/Alert"));
            startDataMonitoring(Paths.get("src/main/resources/application/data"));

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Affiche une alerte sous forme de fenêtre pop-up lorsque des données critiques sont détectées.
     * 
     * @param key   Le nom de la donnée critique.
     * @param room  Le nom de la salle où la donnée est hors seuil.
     * @param value La valeur de la donnée qui est hors seuil.
     */
    private void showAlert(String key, String room, double value) {
        Platform.runLater(() -> {
            Alert alert = new Alert(Alert.AlertType.WARNING);
            alert.setTitle("Alerte : Données critiques");
            alert.setHeaderText(null);
            alert.setContentText("La donnée \"" + key + "\" pour la salle \"" + room + "\" est hors seuil : " + value);
            alert.show();
        });
    }

    /**
     * Démarre la surveillance des alertes dans un répertoire spécifique, en créant un service de surveillance de fichiers.
     * Lorsqu'un nouveau fichier est créé dans ce répertoire, il sera analysé pour détecter des alertes.
     * 
     * @param alertDir Le répertoire à surveiller pour les fichiers d'alertes.
     */
    private void startAlertMonitoring(Path alertDir) {
        alertExecutor = Executors.newSingleThreadExecutor();

        alertExecutor.submit(() -> {
            try {
                if (!Files.exists(alertDir)) {
                    Files.createDirectories(alertDir);
                }

                WatchService watchService = FileSystems.getDefault().newWatchService();
                alertDir.register(watchService, StandardWatchEventKinds.ENTRY_CREATE);

                while (running) {
                    WatchKey key;
                    try {
                        key = watchService.poll();
                        if (key == null) {
                            Thread.sleep(100);
                            continue;
                        }
                    } catch (InterruptedException e) {
                        Thread.currentThread().interrupt();
                        break;
                    }

                    for (WatchEvent<?> event : key.pollEvents()) {
                        if (event.kind() == StandardWatchEventKinds.ENTRY_CREATE) {
                            Path filePath = alertDir.resolve((Path) event.context());
                            // Chargement des alertes (implémentez selon votre logique)
                            // loadAlertData(filePath);
                        }
                    }
                    key.reset();
                }

                watchService.close();
            } catch (IOException e) {
                e.printStackTrace();
            }
        });
    }

    /**
     * Démarre la surveillance des fichiers de données dans un répertoire spécifique.
     * Lorsqu'un nouveau fichier de données est créé dans ce répertoire, il sera chargé pour mettre à jour les graphiques.
     * 
     * @param dataDir Le répertoire à surveiller pour les fichiers de données.
     */
    private void startDataMonitoring(Path dataDir) {
        dataExecutor = Executors.newSingleThreadExecutor();

        dataExecutor.submit(() -> {
            try {
                WatchService watchService = FileSystems.getDefault().newWatchService();
                dataDir.register(watchService, StandardWatchEventKinds.ENTRY_CREATE);

                while (running) {
                    WatchKey key;
                    try {
                        key = watchService.poll();
                        if (key == null) {
                            Thread.sleep(100);
                            continue;
                        }
                    } catch (InterruptedException e) {
                        Thread.currentThread().interrupt();
                        break;
                    }

                    for (WatchEvent<?> event : key.pollEvents()) {
                        if (event.kind() == StandardWatchEventKinds.ENTRY_CREATE) {
                            Path filePath = dataDir.resolve((Path) event.context());
                            // Chargement des données des fichiers (implémentez selon votre logique)
                            // loadSensorData(filePath);
                        }
                    }
                    key.reset();
                }

                watchService.close();
            } catch (IOException e) {
                e.printStackTrace();
            }
        });
    }

    /**
     * Crée un graphique en fonction du type de donnée (barres ou courbes).
     * 
     * @param key   Le nom de la donnée (utilisé pour déterminer si c'est une courbe ou un histogramme).
     * @param data  Les données à afficher dans le graphique.
     * @return Le graphique créé (soit un graphique à barres, soit un graphique linéaire).
     */
    private Chart createChart(String key, Map<String, Double> data) {
        if (key.equalsIgnoreCase("pressure") || key.equalsIgnoreCase("temperature")) {
            return createLineChart(key, data);
        } else {
            return createBarChart(key, data);
        }
    }

    /**
     * Crée un graphique à barres pour afficher les données.
     * 
     * @param key   Le nom de la donnée.
     * @param data  Les données à afficher sous forme de barres.
     * @return Un graphique à barres représentant les données.
     */
    private BarChart<String, Number> createBarChart(String key, Map<String, Double> data) {
        BarChart<String, Number> barChart = new BarChart<>(new CategoryAxis(), new NumberAxis());
        barChart.setTitle("Données : " + key);

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName(key);

        for (Map.Entry<String, Double> entry : data.entrySet()) {
            series.getData().add(new XYChart.Data<>(entry.getKey(), entry.getValue()));
        }

        barChart.getData().add(series);
        return barChart;
    }

    /**
     * Crée un graphique linéaire pour afficher les données.
     * 
     * @param key   Le nom de la donnée.
     * @param data  Les données à afficher sous forme de courbe.
     * @return Un graphique linéaire représentant les données.
     */
    private LineChart<String, Number> createLineChart(String key, Map<String, Double> data) {
        LineChart<String, Number> lineChart = new LineChart<>(new CategoryAxis(), new NumberAxis());
        lineChart.setTitle("Données : " + key);

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName(key);

        for (Map.Entry<String, Double> entry : data.entrySet()) {
            series.getData().add(new XYChart.Data<>(entry.getKey(), entry.getValue()));
        }

        lineChart.getData().add(series);
        return lineChart;
    }

    /**
     * Gère l'événement de clic sur le bouton "Retour" et permet de revenir à l'écran précédent.
     */
    @FXML
    private void handleButtonRetour() {
        System.out.println("Retour à l'écran précédent.");
    }

    /**
     * Arrête les exécutants en cours (surveillance des alertes et des données).
     * Cette méthode doit être appelée lorsque le contrôleur n'est plus nécessaire ou lors de la fermeture de l'application.
     */
    public void stop() {
        try {
            if (alertExecutor != null && !alertExecutor.isShutdown()) {
                alertExecutor.shutdownNow();
            }
            if (dataExecutor != null && !dataExecutor.isShutdown()) {
                dataExecutor.shutdownNow();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
