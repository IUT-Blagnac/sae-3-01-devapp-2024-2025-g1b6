package iut.view;

import iut.App;
import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Tab;
import javafx.scene.control.TabPane;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.NumberAxis;

import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import java.io.FileReader;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.HashMap;
import java.util.Map;
import java.util.stream.Stream;

public class GraphiquesController {

    private Map<String, Double> sensorData = new HashMap<>();

    @FXML
    private TabPane tabPane;

    @FXML
    private Button buttonretour;

    public void initialize() {
        try {
            // Chemin du répertoire contenant les fichiers JSON
            Path dataDir = Paths.get(App.class.getResource("data/B").toURI());

            // Lister tous les fichiers JSON dans le répertoire
            try (Stream<Path> paths = Files.list(dataDir)) {
                paths.filter(Files::isRegularFile)
                    .filter(path -> path.toString().endsWith(".json"))
                    .forEach(path -> {
                        try {
                            loadJsonData(path.toString());
                        } catch (IOException e) {
                            e.printStackTrace();
                        }
                    });
            }

            // Créer dynamiquement des graphiques pour chaque donnée
            for (Map.Entry<String, Double> entry : sensorData.entrySet()) {
                String key = entry.getKey();
                Double value = entry.getValue();

                Tab tab = new Tab(key); // Créer un onglet pour chaque donnée

                // Décider quel type de graphique afficher
                if (isLineChartKey(key)) {
                    tab.setContent(createLineChart(key, value));
                } else {
                    tab.setContent(createBarChart(key, value));
                }

                tabPane.getTabs().add(tab);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Charge les données JSON à partir d'un fichier donné.
     *
     * @param filePath chemin du fichier JSON
     * @throws IOException en cas de problème de lecture
     */
    private void loadJsonData(String filePath) throws IOException {
        JsonElement rootElement = JsonParser.parseReader(new FileReader(filePath));

        if (!rootElement.isJsonArray()) {
            throw new IllegalStateException("Le fichier JSON doit être un tableau.");
        }

        JsonArray jsonArray = rootElement.getAsJsonArray();

        for (JsonElement element : jsonArray) {
            if (element.isJsonObject()) {
                JsonObject jsonObject = element.getAsJsonObject();

                // Si c'est un objet contenant des données de capteurs, on les ajoute
                if (jsonObject.has("temperature")) {
                    for (String key : jsonObject.keySet()) {
                        try {
                            double value = jsonObject.get(key).getAsDouble();
                            sensorData.merge(key, value, Double::sum); // Ajouter au Map en combinant les valeurs
                        } catch (Exception e) {
                            // Ignorer les valeurs non numériques
                        }
                    }
                }
            }
        }
    }

    /**
     * Crée un BarChart pour une donnée spécifique.
     *
     * @param key   nom de la donnée
     * @param value valeur associée
     * @return un graphique à barres
     */
    private BarChart<String, Number> createBarChart(String key, double value) {
        BarChart<String, Number> barChart = new BarChart<>(new CategoryAxis(), new NumberAxis());
        barChart.setTitle("Graphique : " + key);

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.getData().add(new XYChart.Data<>(key, value));
        series.setName("Valeur");

        barChart.getData().add(series);
        return barChart;
    }

    /**
     * Crée un LineChart pour une donnée spécifique.
     *
     * @param key   nom de la donnée
     * @param value valeur associée
     * @return un graphique en lignes
     */
    private LineChart<Number, Number> createLineChart(String key, double value) {
        LineChart<Number, Number> lineChart = new LineChart<>(new NumberAxis(), new NumberAxis());
        lineChart.setTitle("Graphique : " + key);

        XYChart.Series<Number, Number> series = new XYChart.Series<>();
        series.getData().add(new XYChart.Data<>(1, value)); // Ajouter un point (exemple)
        series.setName("Valeur");

        lineChart.getData().add(series);
        return lineChart;
    }

    /**
     * Vérifie si une clé doit être affichée sous forme de LineChart.
     *
     * @param key clé à vérifier
     * @return true si c'est un LineChart, sinon false
     */
    private boolean isLineChartKey(String key) {
        return key.equalsIgnoreCase("temperature")
                || key.equalsIgnoreCase("humidity")
                || key.equalsIgnoreCase("pressure");
    }

    /**
     * Gestionnaire du bouton "Retour".
     */
    @FXML
    private void handleButtonRetour() {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle("Information");
        alert.setHeaderText(null);
        alert.setContentText("Retour à la page précédente.");
        alert.showAndWait();
    }
}