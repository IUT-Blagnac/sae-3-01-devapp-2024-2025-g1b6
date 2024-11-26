package application.view;


import java.io.IOException;

import application.ConfigManager;
import application.LaunchApp;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.RadioButton;
import javafx.scene.control.TextField;
import javafx.scene.control.ToggleGroup;
import javafx.stage.Stage;



public class configViewController {

    @FXML
    TextField hote;

    @FXML
    TextField frequence;

    @FXML
    Button btnRetour;

    @FXML
    Button btnAlerte;

    @FXML
    ListView listCapteur;

    @FXML
    ListView listPanneau;

    @FXML
    ToggleGroup groupCapteur;

    @FXML
    ToggleGroup groupPanneau;


    @FXML
    RadioButton btnOuiCapteur;

    @FXML
    RadioButton btnNonCapteur;

    @FXML
    RadioButton btnOuiPanneau;

    @FXML
    RadioButton btnNonPanneau;

    @FXML
    private void handleToggleCapteur() {
        if (btnOuiCapteur.isSelected()) {
            System.out.println("Oui sélectionné pour Capteurs");
            try {
                ConfigManager.updateConfig("Capteurs", "subscribe_all", "on");
            } catch (IOException e) {
                e.printStackTrace();
            }
        } else if (btnNonCapteur.isSelected()) {
            System.out.println("Non sélectionné pour Capteurs");
            try {
                ConfigManager.updateConfig("Capteurs", "subscribe_all", "off");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    @FXML
    private void handleOpenAlerte() {
        try {
            // Charger la nouvelle vue
            FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("view/seuilsAlerteView.fxml"));
            Parent alertePage = fxmlLoader.load();

            // Afficher dans une nouvelle fenêtre
            Stage stage = new Stage();
            stage.setTitle("Seuils Alerte");
            stage.setScene(new Scene(alertePage));
            stage.show();

            // Optionnel : fermer la fenêtre actuelle
            ((Stage) btnAlerte.getScene().getWindow()).close();

        } catch (IOException e) {
            e.printStackTrace();
        }
    }



    @FXML
    private void handleAddCapteur() {
        String selectedSalle = (String) listCapteur.getSelectionModel().getSelectedItem();
        if (selectedSalle != null) {
            try {
                ConfigManager.updateConfig("Capteurs", "salles", "'" + selectedSalle + "'");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }
    @FXML
    private void handleSubscribeAllCapteurs() {
        if (btnOuiCapteur.isSelected()) {
            try {
                ConfigManager.updateConfig("Capteurs", "subscribe_all", "on");
            } catch (IOException e) {
                e.printStackTrace();
            }
        } else if (btnNonCapteur.isSelected()) {
            try {
                ConfigManager.updateConfig("Capteurs", "subscribe_all", "off");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }



}