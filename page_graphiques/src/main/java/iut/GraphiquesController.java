package iut;

import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.PieChart;
import javafx.scene.chart.XYChart;
import javafx.scene.chart.PieChart.Data;

public class GraphiquesController {

    @FXML
    private BarChart<String, Number> barChart;
    @FXML
    private LineChart<Number, Number> lineChart;
    @FXML
    private PieChart pieChart;

    // Cette méthode est appelée pour initialiser les graphiques
    public void initialize() {
        // Données tests
        XYChart.Series<String, Number> series1 = new XYChart.Series<>();
        series1.setName("Série 1");
        series1.getData().add(new XYChart.Data<>("A", 50));
        series1.getData().add(new XYChart.Data<>("B", 80));
        series1.getData().add(new XYChart.Data<>("C", 40));
        barChart.getData().add(series1);

        // Données tests
        XYChart.Series<Number, Number> series2 = new XYChart.Series<>();
        series2.setName("Série 2");
        series2.getData().add(new XYChart.Data<>(1, 25));
        series2.getData().add(new XYChart.Data<>(2, 50));
        series2.getData().add(new XYChart.Data<>(3, 75));
        lineChart.getData().add(series2);

        // Données tests
        PieChart.Data slice1 = new Data("Secteur A", 30);
        PieChart.Data slice2 = new Data("Secteur B", 40);
        PieChart.Data slice3 = new Data("Secteur C", 30);
        pieChart.getData().addAll(slice1, slice2, slice3);
    }
}

