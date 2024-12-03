package application.view;

import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Alert;
import javafx.scene.control.Tab;
import javafx.scene.control.TabPane;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.NumberAxis;

import java.util.Map;

public class SalleDetailsController {

    @FXML
    private TabPane tabPaneDetails; // Pane pour afficher les graphiques

    private String roomName;
    private Map<String, Double> roomData; // Données de la salle

    public void setRoomData(String roomName, Map<String, Double> roomData) {
        this.roomName = roomName;
        this.roomData = roomData;
        createCharts(); // Générer les graphiques
    }

    /**
     * Crée un graphique pour chaque donnée de la salle.
     */
    private void createCharts() {
        for (Map.Entry<String, Double> entry : roomData.entrySet()) {
            String key = entry.getKey();
            Double value = entry.getValue();

            Tab tab = new Tab(key); // Créer un onglet pour chaque donnée

            if (isLineChartKey(key)) {
                tab.setContent(createLineChart(key, value));
            } else {
                tab.setContent(createBarChart(key, value));
            }

            tabPaneDetails.getTabs().add(tab);
        }
    }

    private BarChart<String, Number> createBarChart(String key, double value) {
        BarChart<String, Number> barChart = new BarChart<>(new CategoryAxis(), new NumberAxis());
        barChart.setTitle("BarChart : " + key);

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Valeur");
        series.getData().add(new XYChart.Data<>(key, value));

        barChart.getData().add(series);
        return barChart;
    }

    private LineChart<Number, Number> createLineChart(String key, double value) {
        LineChart<Number, Number> lineChart = new LineChart<>(new NumberAxis(), new NumberAxis());
        lineChart.setTitle("LineChart : " + key);

        XYChart.Series<Number, Number> series = new XYChart.Series<>();
        series.setName("Valeur");
        series.getData().add(new XYChart.Data<>(1, value));

        lineChart.getData().add(series);
        return lineChart;
    }

    private boolean isLineChartKey(String key) {
        return key.equalsIgnoreCase("temperature")
                || key.equalsIgnoreCase("humidity")
                || key.equalsIgnoreCase("pressure");
    }

    @FXML
    private void handleRetourGeneral() {
        tabPaneDetails.getScene().getWindow().hide(); // Fermer la fenêtre actuelle
    }

    @FXML
    private void handleQuitter() {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle("Quitter");
        alert.setHeaderText("Fermeture de l'application");
        alert.setContentText("Fermeture de l'application");
        alert.showAndWait();
        System.exit(0);
    }

}
