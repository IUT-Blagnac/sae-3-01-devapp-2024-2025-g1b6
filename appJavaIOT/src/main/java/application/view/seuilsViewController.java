package application.view;


import java.io.IOException;

import application.LaunchApp;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.MenuButton;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

public class seuilsViewController {

    @FXML
    TextField seuilsMin;

    @FXML
    TextField SeuilMax;

    @FXML
    Button btnRetour;

    @FXML
    MenuButton typeDonnees;

    @FXML
    private void handleOpenConfig() {
        try {
            // Charger la nouvelle vue
            FXMLLoader fxmlLoader = new FXMLLoader(LaunchApp.class.getResource("view/configView.fxml"));

            Parent config = fxmlLoader.load();

            // Afficher dans une nouvelle fenêtre
            Stage stage = new Stage();
            stage.setTitle("Configuration");
            stage.setScene(new Scene(config));
            stage.show();

            // Optionnel : fermer la fenêtre actuelle
            ((Stage) btnRetour.getScene().getWindow()).close();

        } catch (IOException e) {
            e.printStackTrace();
        }
    }

}