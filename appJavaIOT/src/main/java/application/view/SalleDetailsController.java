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
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import java.io.FileReader;
import java.io.IOException;
import java.nio.file.*;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

/**
 * Contrôleur pour afficher les détails d'une salle et gérer les graphiques des données de capteurs.
 */
public class SalleDetailsController {

    private ExecutorService alertExecutor;
    private ExecutorService dataExecutor;
    private volatile boolean running = true;

    @FXML
    private TabPane tabPane; // Conteneur pour les onglets

    @FXML
    private Button buttonRetour; // Bouton de retour

    /**
     * Méthode d'initialisation qui charge les données des salles et crée les graphiques.
     */
    public void initialize() {
        try {
            SyncData syncData = SyncData.getInstance();
            syncData.fillRoomList();  // Charger les données des salles

            // Créer les onglets pour chaque salle
            for (Room room : syncData.getRoomsMap().values()) {
                createRoomTab(room);
            }

            // Lancer les threads pour surveiller les alertes et les nouveaux fichiers JSON
            startAlertMonitoring(Paths.get("src/main/resources/application/data/Alert"));
            startDataMonitoring(Paths.get("src/main/resources/application/data"));

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    
    private void showAlert(String key, String room, double value) {
        Platform.runLater(() -> {
            Alert alert = new Alert(Alert.AlertType.WARNING);
            alert.setTitle("Alerte : Données critiques");
            alert.setHeaderText(null);
            alert.setContentText("La donnée \"" + key + "\" pour la salle \"" + room + "\" est hors seuil : " + value);
            alert.show();
        });
    }

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
     * Crée un onglet pour chaque salle et l'ajoute au TabPane.
     */
    private void createRoomTab(Room room) {
        Tab tab = new Tab(room.getRoomName());
        tab.setContent(createRoomChart(room));
        tabPane.getTabs().add(tab);
    }

    /**
     * Crée un graphique pour la salle donnée.
     */
    private Chart createRoomChart(Room room) {
        BarChart<String, Number> barChart = new BarChart<>(new CategoryAxis(), new NumberAxis());
        barChart.setTitle("Données pour la salle : " + room.getRoomName());

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName(room.getRoomName());

        for (Measure measure : room.getRoomValues()) {
            for (Map.Entry<String, Object> entry : measure.getValues().entrySet()) {
                String key = entry.getKey();
                Double value = ((Number) entry.getValue()).doubleValue();
                series.getData().add(new XYChart.Data<>(key, value));

        
            }
        }

        barChart.getData().add(series);
        return barChart;
    }


    /**
     * Charge les données d'un fichier de capteur.
     */
    private void loadSensorData(Path filePath) throws IOException {
        JsonElement rootElement = JsonParser.parseReader(new FileReader(filePath.toFile()));
        if (rootElement.isJsonArray()) {
            JsonArray rootArray = rootElement.getAsJsonArray();
            for (JsonElement element : rootArray) {
                JsonObject dataObject = element.getAsJsonObject();
                String roomName = dataObject.get("room").getAsString();
                JsonArray measures = dataObject.getAsJsonArray("measures");

                for (JsonElement measureElement : measures) {
                    JsonObject measureObject = measureElement.getAsJsonObject();
                    Map<String, Object> valuesMap = new HashMap<>();
                    boolean alertMeasure = measureObject.get("alertMeasure").getAsBoolean();

                    for (Map.Entry<String, JsonElement> entry : measureObject.entrySet()) {
                        if (!entry.getKey().equals("alertMeasure")) {
                            valuesMap.put(entry.getKey(), entry.getValue().getAsDouble());
                        }
                    }

                    Measure measure = new Measure(valuesMap, alertMeasure);
                    Room room = findRoomByName(roomName);
                    if (room != null) {
                        room.addRoomValue(measure);
                    }
                }
            }
        }
    }

    /**
     * Surveille le répertoire des données et charge les fichiers JSON lorsqu'ils sont créés.
     */
    private void startDataMonitoring(Path dataDir) {
        dataExecutor = Executors.newSingleThreadExecutor();
        dataExecutor.submit(() -> {
            try {
                WatchService watchService = FileSystems.getDefault().newWatchService();
                dataDir.register(watchService, StandardWatchEventKinds.ENTRY_CREATE);

                while (running) {
                    WatchKey key = watchService.take();
                    for (WatchEvent<?> event : key.pollEvents()) {
                        if (event.kind() == StandardWatchEventKinds.ENTRY_CREATE) {
                            Path filePath = dataDir.resolve((Path) event.context());
                            Platform.runLater(() -> {
                                try {
                                    loadSensorData(filePath);
                                    updateCharts(); // Met à jour les graphiques
                                } catch (IOException e) {
                                    e.printStackTrace();
                                }
                            });
                        }
                    }
                    key.reset();
                }
            } catch (IOException | InterruptedException e) {
                e.printStackTrace();
            }
        });
    }

    /**
     * Met à jour les graphiques avec les nouvelles données.
     */
    private void updateCharts() {
        Platform.runLater(() -> {
            for (Tab tab : tabPane.getTabs()) {
                Room room = findRoomByName(tab.getText()); // Récupérer la salle associée
                if (room != null) {
                    tab.setContent(createRoomChart(room)); // Mettre à jour le graphique
                }
            }
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
        running = false;
        if (alertExecutor != null) alertExecutor.shutdownNow();
        if (dataExecutor != null) dataExecutor.shutdownNow();
    }

    // Méthode fictive pour trouver une salle par son nom
    private Room findRoomByName(String roomName) {
        SyncData syncData = SyncData.getInstance();
        return syncData.getRoomsMap().get(roomName);
    }
}