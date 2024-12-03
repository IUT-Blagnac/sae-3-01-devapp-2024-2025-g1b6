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
 * Contrôleur pour la gestion des graphiques dans l'application.
 * Ce contrôleur charge les données à partir des fichiers JSON, les affiche sous forme de graphiques et surveille les alertes.
 */
public class GraphiquesController {

    private Map<String, Map<String, Double>> sensorData = new HashMap<>();
    private ExecutorService alertExecutor;
    private ExecutorService dataExecutor;
    private volatile boolean running = true;

    @FXML
    private TabPane tabPane; // Conteneur pour les onglets

    @FXML
    private Button buttonRetour; // Bouton de retour

    /**
     * Méthode d'initialisation qui charge les fichiers JSON, crée les graphiques
     * et lance les threads pour surveiller les alertes et les nouveaux fichiers.
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

            // Créer les onglets pour chaque type de donnée
            for (String key : sensorData.keySet()) {
                Tab tab = new Tab(key);
                tab.setContent(createChart(key, sensorData.get(key))); // Choisir un type de graphique
                tabPane.getTabs().add(tab);
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

        for (Map.Entry<String, JsonElement> entry : dataObject.entrySet()) {
            String key = entry.getKey();
            double value = entry.getValue().getAsDouble();

            if (isAlertFile) {
                showAlert(key, room, value);
            } else {
                sensorData.computeIfAbsent(key, k -> new HashMap<>()).put(room, value);
            }
        }
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
     * Mise à jour des graphiques avec les nouvelles données.
     */
    private void updateCharts(Path filePath) {
        // Pour chaque type de donnée, vous pouvez vérifier et mettre à jour les graphiques correspondants
        Platform.runLater(() -> {
            for (Tab tab : tabPane.getTabs()) {
                // Vous pouvez choisir de mettre à jour les graphiques en fonction du fichier.
                // Par exemple, en comparant les clés de sensorData avec les noms des onglets.
            }
        });
    }

    /**
     * Crée un graphique en fonction du type de donnée (barres ou lignes).
     * 
     * @param key La clé de la donnée à afficher
     * @param data Les données à afficher
     * @return Le graphique créé (BarChart ou LineChart)
     */
    private Chart createChart(String key, Map<String, Double> data) {
        if (key.equalsIgnoreCase("pressure") || key.equalsIgnoreCase("temperature")) {
            return createLineChart(key, data); // LineChart pour les séries continues
        } else {
            return createBarChart(key, data); // BarChart par défaut
        }
    }

    /**
     * Crée un BarChart pour afficher les données sous forme de barres.
     * 
     * @param key La clé de la donnée à afficher
     * @param data Les données à afficher
     * @return Le BarChart créé
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
     * Crée un LineChart pour afficher les données sous forme de lignes.
     * 
     * @param key La clé de la donnée à afficher
     * @param data Les données à afficher
     * @return Le LineChart créé
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
