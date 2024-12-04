package application.view;

import application.model.Solar;
import application.model.SyncData;
import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.XYChart;

import java.util.List;

public class SolarPanelController {

    @FXML
    private LineChart<String, Number> energyChart;

    private XYChart.Series<String, Number> energySeries;

    /**
     * Initialisation du contrôleur.
     * Configure le graphique et charge les données des panneaux solaires.
     */
    @FXML
    public void initialize() {
        // Initialiser la série pour les données
        energySeries = new XYChart.Series<>();
        energySeries.setName("Énergie Produite");

        // Charger les données des panneaux solaires depuis SyncData
        List<Solar> solarPanels = SyncData.getInstance().getSolarPanelValues();

        // Vérifier s'il y a des données disponibles
        if (solarPanels == null || solarPanels.isEmpty()) {
            System.out.println("Aucune donnée solaire disponible.");
            return;
        }

        // Ajouter les données du premier panneau solaire au graphique
        Solar solar = solarPanels.get(0); // Exemple : le premier panneau
        updateEnergyChart(solar);

        // Ajouter la série au graphique
        energyChart.getData().add(energySeries);
    }

    /**
     * Met à jour le graphique avec les données d'un panneau solaire spécifique.
     *
     * @param solar Le panneau solaire dont les données doivent être affichées.
     */
    private void updateEnergyChart(Solar solar) {
        // Effacer les anciennes données
        energySeries.getData().clear();

        // Ajouter les nouvelles données
        for (var entry : solar.getEnergyMap().entrySet()) {
            String xValue = entry.getKey();    // Exemple : "lifeTimeData"
            Float yValue = entry.getValue();  // Valeur associée

            // Ajouter au graphique
            System.out.println("Ajout au graphique : X = " + xValue + ", Y = " + yValue);
            energySeries.getData().add(new XYChart.Data<>(xValue, yValue));
        }
    }

    /**
     * Méthode appelée lors de la fermeture de l'application.
     * Permet de libérer les ressources ou effectuer des opérations de nettoyage.
     */
    public void stop() {
        System.out.println("Nettoyage des ressources du contrôleur SolarPanel.");
    }
}
