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

import java.io.IOException;
import java.nio.file.*;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

public class GraphiquesController {

    private Map<String, Map<String, Double>> sensorData = new HashMap<>();
    private volatile boolean running = true;

    @FXML
    private TabPane tabPane;

    @FXML
    private Button buttonRetour;

    public void initialize() {
        try {
            SyncData syncData = SyncData.getInstance();

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

            for (String key : sensorData.keySet()) {
                Tab tab = new Tab(key);
                tab.setContent(createChart(key, sensorData.get(key)));
                tabPane.getTabs().add(tab);
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }



    private Chart createChart(String key, Map<String, Double> data) {
        if (key.equalsIgnoreCase("pressure") || key.equalsIgnoreCase("temperature")) {
            return createLineChart(key, data);
        } else {
            return createBarChart(key, data);
        }
    }

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

    @FXML
    private void handleButtonRetour() {
        System.out.println("Retour à l'écran précédent.");
    }

}