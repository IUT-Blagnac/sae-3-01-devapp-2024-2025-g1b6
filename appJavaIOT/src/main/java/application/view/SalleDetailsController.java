package application.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.chart.*;
import javafx.scene.control.Tab;
import javafx.scene.control.TabPane;
import javafx.stage.Stage;
import model.data.Measure;
import model.data.Room;

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
    private Stage containingStage;

    @FXML
    private TabPane tabPane;


    public void initContext(Stage containingStage){
        this.containingStage = containingStage;
    }


    /**
     * Initialise les détails d'une salle spécifique en configurant les onglets et en démarrant la surveillance des alertes et des données.
     *
     * @param room La salle à afficher, contenant les données et les alertes à surveiller.
     */
    public void setRoom(Room room) {
        this.room = room;
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
        stop(); // Libérer les ressources avant de changer de vue
        this.containingStage.close();
    }
}
