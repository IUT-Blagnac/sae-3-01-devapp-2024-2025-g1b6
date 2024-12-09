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

import java.io.IOException;
import java.nio.file.*;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

/**
 * Contrôleur pour afficher les détails d'une salle spécifique, y compris les graphiques et la surveillance des alertes et des données.
 * <p>
 * Cette classe est responsable de l'affichage des informations relatives à une salle, comme les graphiques des données de la salle, et la gestion des alertes et des données provenant de fichiers externes.
 * Elle utilise un mécanisme de surveillance des fichiers pour détecter les changements dans des répertoires spécifiques et actualiser les graphiques en conséquence.
 * </p>
 * 
 * @author Marwane Ibrahim
 */
public class SalleDetailsController {

    private ExecutorService alertExecutor;
    private ExecutorService dataExecutor;
    private volatile boolean running = true;
    private Room room; // Salle spécifique associée à ce contrôleur

    @FXML
    private TabPane tabPane;

    @FXML
    private Button buttonRetour;

    /**
     * Initialise les détails d'une salle spécifique en configurant les onglets et en démarrant la 
     * surveillance des alertes et des données.
     * 
     * @see Room
     * @param room La salle à afficher, contenant les données et les alertes à surveiller.
     */
    public void setRoom(Room room) {
        this.room = room;

        if (room != null) {
            createRoomTab(room);
            startAlertMonitoring(Paths.get("src/main/resources/application/data/Alert"));
            startDataMonitoring(Paths.get("src/main/resources/application/data"));
        } else {
            System.err.println("Room is null, unable to initialize SalleDetailsController.");
        }
    }

    /**
     * Affiche une alerte sous forme de fenêtre pop-up lorsqu'une donnée dépasse un seuil critique.
     * 
     * @param key      Le nom de la donnée qui est hors seuil.
     * @param roomName Le nom de la salle où la donnée est hors seuil.
     * @param value    La valeur de la donnée qui a dépassé le seuil.
     */
    private void showAlert(String key, String roomName, double value) {
        Platform.runLater(() -> {
            Alert alert = new Alert(Alert.AlertType.WARNING);
            alert.setTitle("Alerte : Données critiques");
            alert.setHeaderText(null);
            alert.setContentText("La donnée \"" + key + "\" pour la salle \"" + roomName + "\" est hors seuil : " + value);
            alert.show();
        });
    }

    /**
     * Démarre la surveillance des alertes dans un répertoire spécifique, en créant un service de surveillance des fichiers.
     * Lorsqu'un nouveau fichier est créé, il sera analysé pour détecter une alerte.
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
                            // Implémentez la logique pour charger les alertes si nécessaire
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
     * Démarre la surveillance des données dans un répertoire spécifique.
     * Lorsqu'un nouveau fichier de données est créé, il sera chargé et les graphiques seront mis à jour.
     * 
     * @param dataDir Le répertoire à surveiller pour les fichiers de données.
     */
    private void startDataMonitoring(Path dataDir) {
        dataExecutor = Executors.newSingleThreadExecutor();
    
        dataExecutor.submit(() -> {
            try (WatchService watchService = FileSystems.getDefault().newWatchService()) {
                dataDir.register(watchService, StandardWatchEventKinds.ENTRY_CREATE);
    
                while (running) {
                    WatchKey key = watchService.poll(); // Attente non bloquante
                    if (key == null) {
                        try {
                            Thread.sleep(100); // Pause
                        } catch (InterruptedException e) {
                            // Arrêt propre si interrompu
                            Thread.currentThread().interrupt(); // Préserve l'état d'interruption
                            break;
                        }
                        continue;
                    }
    
                    for (WatchEvent<?> event : key.pollEvents()) {
                        if (event.kind() == StandardWatchEventKinds.ENTRY_CREATE) {
                            Path filePath = dataDir.resolve((Path) event.context());
                            Platform.runLater(() -> {
                                updateCharts();
                            });
                        }
                    }
                    key.reset();
                }
            } catch (IOException e) {
                e.printStackTrace();
            }
        });
    }

    /**
     * Crée un onglet avec un graphique pour afficher les données d'une salle.
     * 
     * @param room La salle dont les données seront affichées dans le graphique.
     */
    private void createRoomTab(Room room) {
        Tab tab = new Tab("Graphiques - " + room.getRoomName());
        tab.setContent(createRoomChart(room));
        tabPane.getTabs().clear(); // Supprime les anciens onglets
        tabPane.getTabs().add(tab); // Ajoute le nouvel onglet
    }

    /**
     * Crée un graphique à barres pour afficher les données d'une salle.
     * 
     * @param room La salle dont les données seront affichées dans le graphique.
     * @return Un graphique à barres représentant les données de la salle.
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
     * Met à jour les graphiques de la salle en ajoutant de nouvelles données.
     */
    private void updateCharts() {
        Platform.runLater(() -> createRoomTab(room));
    }

    /**
     * Méthode appelée pour libérer les ressources lorsque le contrôleur est arrêté ou lorsque l'application se ferme.
     * Cette méthode arrête les exécutants en cours et libère toutes les ressources.
     */
    public void stop() {
        running = false;

        if (alertExecutor != null && !alertExecutor.isShutdown()) {
            alertExecutor.shutdownNow();
        }

        if (dataExecutor != null && !dataExecutor.isShutdown()) {
            dataExecutor.shutdownNow();
        }
    }

    /**
     * Méthode pour gérer l'événement de clic sur le bouton "Retour", permettant de quitter la vue actuelle.
     */
    @FXML
    private void handleQuitter() {
        System.out.println("Retour à l'écran précédent.");
        stop(); // Libérer les ressources avant de changer de vue
    }
}
