package application.view;

import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
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
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.HashMap;
import java.util.Map;
import java.util.stream.Stream;

public class GraphiquesController {

    // Stockage centralisé des données : clé = type de donnée (ex. "temperature")
    private Map<String, Map<String, Double>> sensorData = new HashMap<>();

    @FXML
    private TabPane tabPane;

    @FXML
    private Button buttonretour;

    public void initialize() {
        try {
            // Lecture des fichiers JSON dans le répertoire "data"
            Path dataDir = Paths.get(App.class.getResource("data").toURI());

            try (Stream<Path> paths = Files.walk(dataDir)) {
                paths.filter(Files::isRegularFile) // Seulement les fichiers
                     .filter(path -> path.toString().endsWith(".json")) // Filtrer les JSON
                     .forEach(path -> {
                         try {
                             loadJsonData(path); // Charger chaque fichier
                         } catch (IOException e) {
                             e.printStackTrace();
                         }
                     });
            }

            // Créer les onglets pour chaque type de donnée
            for (String key : sensorData.keySet()) {
                Tab tab = new Tab(key);
                tab.setContent(createBarChart(key, sensorData.get(key))); // Générer un graphique
                tabPane.getTabs().add(tab);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Lecture et traitement des données d'un fichier JSON.
     */
    private void loadJsonData(Path filePath) throws IOException {
        JsonElement rootElement = JsonParser.parseReader(new FileReader(filePath.toFile()));
    
        // Vérifier que la racine est un tableau
        if (!rootElement.isJsonArray()) {
            throw new IllegalStateException("Le fichier JSON doit être un tableau racine.");
        }
    
        JsonArray rootArray = rootElement.getAsJsonArray();
        if (rootArray.size() != 1) {
            throw new IllegalStateException("Le fichier JSON doit contenir un tableau unique avec deux objets.");
        }
    
        // Accéder au tableau imbriqué
        JsonArray nestedArray = rootArray.get(0).getAsJsonArray();
        if (nestedArray.size() != 2) {
            throw new IllegalStateException("Le tableau imbriqué doit contenir exactement deux objets.");
        }
    
        // Extraire les deux objets
        JsonObject dataObject = nestedArray.get(0).getAsJsonObject();
        JsonObject metadataObject = nestedArray.get(1).getAsJsonObject();
    
        // Obtenir uniquement le nom de la salle
        String room = metadataObject.get("room").getAsString();
    
        // Parcourir les données des capteurs
        for (Map.Entry<String, JsonElement> entry : dataObject.entrySet()) {
            String key = entry.getKey();
            double value = entry.getValue().getAsDouble();
    
            // Utiliser uniquement le nom de la salle (pas "bâtiment + salle")
            String sensorKey = key + " (" + room + ")";
            sensorData.computeIfAbsent(key, k -> new HashMap<>()).put(sensorKey, value); // Ajouter ou mettre à jour
        }
    }        
    

    /**
     * Créer un BarChart pour une donnée spécifique.
     */
    private BarChart<String, Number> createBarChart(String key, Map<String, Double> data) {
        BarChart<String, Number> barChart = new BarChart<>(new CategoryAxis(), new NumberAxis());
        barChart.setTitle("Données : " + key);

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName(key);

        // Ajouter les valeurs des salles au graphique
        for (Map.Entry<String, Double> entry : data.entrySet()) {
            series.getData().add(new XYChart.Data<>(entry.getKey(), entry.getValue()));
        }

        barChart.getData().add(series);
        return barChart;
    }

    /**
     * Gestion du bouton "Retour".
     */
    @FXML
    private void handleButtonRetour() {
        System.out.println("Retour à l'écran précédent.");
    }
}
