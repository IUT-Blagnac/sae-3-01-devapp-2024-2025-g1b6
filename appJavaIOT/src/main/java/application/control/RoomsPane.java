package application.control;

import application.LaunchApp;
import application.view.RoomsViewControler;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;

import java.io.IOException;

/**
 * La classe RoomsPane est responsable de la gestion de la vue des salles.
 * Elle charge la vue des salles à partir d'un fichier FXML et configure la scène et la fenêtre.
 *
 * <p>Cette classe utilise {@link RoomsViewControler} pour gérer les interactions utilisateur avec la vue.
 *
 * @see application.LaunchApp
 * @see application.tools.StageManagement
 * @see application.view.RoomsViewControler
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class RoomsPane {

    private Stage rpStage;
    private RoomsViewControler rpViewController;

    /**
     * Constructeur de RoomsPane.
     * Initialise la fenêtre de la vue des salles et configure ses paramètres.
     *
     * @param parentStage La scène parente de cette fenêtre.
     */
    public RoomsPane(Stage parentStage) {
        try {
            FXMLLoader loader = new FXMLLoader(LaunchApp.class.getResource("view/RoomsView.fxml"));
            BorderPane root = loader.load();

            Scene scene = new Scene(root, root.getPrefWidth() + 20, root.getPrefHeight() + 10);

            this.rpStage = new Stage();
            this.rpStage.initModality(Modality.WINDOW_MODAL);
            this.rpStage.initOwner(parentStage);
            this.rpStage.setScene(scene);
            this.rpStage.setTitle("Affichage des dernières mesures par salle");
            this.rpStage.setResizable(false);
            this.rpViewController = loader.getController();
            this.rpViewController.initContext(this.rpStage);

        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    /**
     * Affiche la fenêtre des salles.
     * Appelle la méthode {@link RoomsViewControler#show()} du contrôleur de la vue des salles.
     */
    public void show() {
        this.rpViewController.show();
    }
}
