package application.view;

import javafx.fxml.FXML;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.XYChart;
import model.data.Solar;
import application.SyncData;

import java.util.List;


/**
 * Contrôleur pour gérer le graphique des panneaux solaires.
 */
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

        // Charger les données des panneaux solaires
        loadSolarData();

        // Ajouter la série au graphique
        energyChart.getData().add(energySeries);
    }

    /**
     * Charge les données des panneaux solaires depuis SyncData.
     */
    private void loadSolarData() {
        List<Solar> solarPanels = SyncData.getInstance().getSolarPanelValues();

        // Vérifier s'il y a des données disponibles
        if (solarPanels == null || solarPanels.isEmpty()) {
            System.out.println("Aucune donnée solaire disponible.");
            return;
        }

        // Met à jour le graphique avec les données du premier panneau solaire
        for (Solar solar : solarPanels) {
            updateEnergyChart(solar);
        }
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
        solar.getEnergyMap().forEach((xValue, yValue) -> {
            System.out.println("Ajout au graphique : X = " + xValue + ", Y = " + yValue);
            energySeries.getData().add(new XYChart.Data<>(xValue, yValue));
        });

        // Vérifie si des données ont été ajoutées
        if (energySeries.getData().isEmpty()) {
            System.out.println("Aucune donnée ajoutée au graphique pour le panneau solaire.");
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