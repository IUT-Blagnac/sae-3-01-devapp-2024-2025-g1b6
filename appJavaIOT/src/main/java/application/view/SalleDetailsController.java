package application.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.chart.*;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Tab;
import javafx.scene.control.TabPane;
import model.data.Measure;
import model.data.Room;
import application.SyncData;

import java.util.Map;
import java.util.concurrent.ExecutorService;

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

            // Créer les onglets pour chaque salle
            for (Room room : syncData.getRoomsMap().values()) {
                createRoomTab(room);
            }

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