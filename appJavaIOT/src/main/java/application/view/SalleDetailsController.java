package application.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.chart.*;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Tab;
import javafx.scene.control.TabPane;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import application.App;

import java.io.FileReader;
import java.io.IOException;
import java.nio.file.*;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.stream.Stream;

/**
 * Contrôleur pour afficher les détails d'une salle et gérer les graphiques des données de capteurs.
 * Cette classe est responsable de charger les fichiers JSON, afficher les alertes et créer des graphiques pour chaque salle.
 */
public class SalleDetailsController {

    private Map<String, Map<String, Double>> sensorData = new HashMap<>();
    private ExecutorService alertExecutor;
    private ExecutorService dataExecutor;
    private volatile boolean running = true;

    @FXML
    private TabPane tabPane; // Conteneur pour les onglets

    @FXML
    private Button buttonRetour; // Bouton de retour

    /**
     * Méthode d'initialisation qui charge les fichiers JSON, crée les graphiques pour chaque salle,
     * et lance les threads pour surveiller les alertes et les nouveaux fichiers JSON.
     */
    public void initialize() {
        try {
            Path dataDir = Paths.get(App.class.getResource("data").toURI());

            // Charger les fichiers de données existants
            try (Stream<Path> paths = Files.walk(dataDir)) {
                paths.filter(Files::isRegularFile)
                     .filter(path -> path.toString().endsWith(".json"))
                     .forEach(path -> {
                         try {
                             boolean isAlertFile = isInAlertDirectory(dataDir, path);
                             loadJsonData(path, isAlertFile);
                         } catch (IOException e) {
                             e.printStackTrace();
                         }
                     });
            }

            // Lancer les threads pour surveiller les alertes et les nouveaux fichiers JSON
            startAlertMonitoring(dataDir.resolve("Alert"));
            startDataMonitoring(dataDir);

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Vérifie si le fichier donné se trouve dans le répertoire des alertes.
     * 
     * @param baseDir Le répertoire de base des données
     * @param filePath Le chemin du fichier à vérifier
     * @return True si le fichier est dans le répertoire des alertes, sinon False
     */
    private boolean isInAlertDirectory(Path baseDir, Path filePath) {
        Path relativePath = baseDir.relativize(filePath);
        return relativePath.getParent() != null && relativePath.getParent().toString().contains("Alert");
    }

    /**
     * Charge les données d'un fichier JSON et met à jour la carte des données du capteur.
     * Si le fichier est un fichier d'alerte, une alerte sera affichée.
     * 
     * @param filePath Le chemin du fichier JSON à charger
     * @param isAlertFile True si c'est un fichier d'alerte, sinon False
     * @throws IOException Si une erreur de lecture du fichier se produit
     */
    private void loadJsonData(Path filePath, boolean isAlertFile) throws IOException {
        JsonElement rootElement = JsonParser.parseReader(new FileReader(filePath.toFile()));

        if (!rootElement.isJsonArray()) {
            throw new IllegalStateException("Le fichier JSON doit être un tableau racine.");
        }

        JsonArray rootArray = rootElement.getAsJsonArray();
        if (rootArray.size() != 1) {
            throw new IllegalStateException("Le fichier JSON doit contenir un tableau unique avec deux objets.");
        }

        JsonArray nestedArray = rootArray.get(0).getAsJsonArray();
        if (nestedArray.size() != 2) {
            throw new IllegalStateException("Le tableau imbriqué doit contenir exactement deux objets.");
        }

        JsonObject dataObject = nestedArray.get(0).getAsJsonObject();
        JsonObject metadataObject = nestedArray.get(1).getAsJsonObject();

        String room = metadataObject.get("room").getAsString();

        // Enregistrer les données par type de capteur et salle
        for (Map.Entry<String, JsonElement> entry : dataObject.entrySet()) {
            String key = entry.getKey();
            double value = entry.getValue().getAsDouble();

            sensorData.computeIfAbsent(key, k -> new HashMap<>()).put(room, value);
        }

        // Créer un onglet pour chaque salle
        createRoomTab(room, dataObject);
    }

    /**
     * Affiche une alerte si les données du fichier d'alerte sont critiques.
     * 
     * @param key La clé de la donnée
     * @param room Le nom de la salle
     * @param value La valeur de la donnée
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
     * Crée un onglet pour chaque salle et l'ajoute au TabPane.
     * 
     * @param roomName Le nom de la salle à afficher
     * @param dataObject Les données à afficher pour cette salle
     */
    private void createRoomTab(String roomName, JsonObject dataObject) {
        Platform.runLater(() -> {
            // Créer un onglet dont le titre est le nom de la salle
            Tab tab = new Tab(roomName);
            tab.setContent(createRoomChart(roomName, dataObject));
            tabPane.getTabs().add(tab);
        });
    }

    /**
     * Crée un graphique pour une salle donnée, ici un graphique à barres représentant les données du capteur.
     * 
     * @param roomName Le nom de la salle
     * @param dataObject Les données à afficher pour la salle
     * @return Le graphique créé (ici un BarChart)
     */
    private Chart createRoomChart(String roomName, JsonObject dataObject) {
        BarChart<String, Number> barChart = new BarChart<>(new CategoryAxis(), new NumberAxis());
        barChart.setTitle("Données pour la salle : " + roomName);

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName(roomName);

        // Ajouter les données liées à la salle au graphique
        for (Map.Entry<String, JsonElement> entry : dataObject.entrySet()) {
            series.getData().add(new XYChart.Data<>(entry.getKey(), entry.getValue().getAsDouble()));
        }

        barChart.getData().add(series);
        return barChart;
    }

    /**
     * Surveille le répertoire des alertes et charge les fichiers JSON d'alerte lorsqu'ils sont créés.
     * 
     * @param alertDir Le répertoire des alertes à surveiller
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
                        key = watchService.poll(); // Utiliser poll() pour éviter le blocage
                        if (key == null) {
                            Thread.sleep(100); // Pause pour éviter de surcharger le CPU
                            continue;
                        }
                    } catch (InterruptedException e) {
                        Thread.currentThread().interrupt();
                        break;
                    }

                    for (WatchEvent<?> event : key.pollEvents()) {
                        WatchEvent.Kind<?> kind = event.kind();

                        if (kind == StandardWatchEventKinds.ENTRY_CREATE) {
                            Path filePath = alertDir.resolve((Path) event.context());
                            Platform.runLater(() -> {
                                try {
                                    loadJsonData(filePath, true);
                                } catch (IOException e) {
                                    e.printStackTrace();
                                }
                            });
                        }
                    }
                    key.reset();
                }

                watchService.close(); // Fermer proprement le WatchService
            } catch (IOException e) {
                e.printStackTrace();
            }
        });
    }

    /**
     * Surveille le répertoire des données et charge les fichiers JSON lorsqu'ils sont créés.
     * Met également à jour les graphiques avec les nouvelles données.
     * 
     * @param dataDir Le répertoire des données à surveiller
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
                        key = watchService.poll(); // Utiliser poll() pour éviter le blocage
                        if (key == null) {
                            Thread.sleep(100); // Pause pour éviter de surcharger le CPU
                            continue;
                        }
                    } catch (InterruptedException e) {
                        Thread.currentThread().interrupt();
                        break;
                    }

                    for (WatchEvent<?> event : key.pollEvents()) {
                        WatchEvent.Kind<?> kind = event.kind();

                        if (kind == StandardWatchEventKinds.ENTRY_CREATE) {
                            Path filePath = dataDir.resolve((Path) event.context());
                            Platform.runLater(() -> {
                                try {
                                    boolean isAlertFile = isInAlertDirectory(dataDir, filePath);
                                    loadJsonData(filePath, isAlertFile);
                                    if (!isAlertFile) {
                                        // Si ce n'est pas un fichier d'alerte, mettez à jour les graphiques
                                        updateCharts(filePath);
                                    }
                                } catch (IOException e) {
                                    e.printStackTrace();
                                }
                            });
                        }
                    }
                    key.reset();
                }

                watchService.close(); // Fermer proprement le WatchService
            } catch (IOException e) {
                e.printStackTrace();
            }
        });
    }

    /**
     * Met à jour les graphiques avec les nouvelles données si nécessaire.
     * 
     * @param filePath Le chemin du fichier qui contient les nouvelles données
     */
    private void updateCharts(Path filePath) {
        Platform.runLater(() -> {
            // Mettre à jour les graphiques avec les nouvelles données si nécessaire
        });
    }

    /**
     * Gère le bouton de retour à l'écran précédent.
     */
    @FXML
    private void handleButtonRetour() {
        System.out.println("Retour à l'écran précédent.");
    }

    /**
     * Arrête les exécuteurs et libère les ressources utilisées.
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
