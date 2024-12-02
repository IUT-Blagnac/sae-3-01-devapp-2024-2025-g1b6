package application.control;

import application.LaunchApp;
import application.tools.StageManagement;
import application.view.RoomsViewControler;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;

import java.io.IOException;

public class RoomsPane {

    private Stage rpStage;
    private RoomsViewControler rpViewController;


    public RoomsPane(Stage parentStage){
        try {
            FXMLLoader loader = new FXMLLoader(LaunchApp.class.getResource("view/RoomsView.fxml"));
            BorderPane root = loader.load();

            Scene scene = new Scene(root, root.getPrefWidth() +20, root.getPrefHeight() + 10);

            this.rpStage = new Stage();
            this.rpStage.initModality(Modality.WINDOW_MODAL);
            this.rpStage.initOwner(parentStage);
            this.rpStage.setScene(scene);
            this.rpStage.setTitle("Affichage des dernières mesures par salle");
            this.rpStage.setResizable(false);
            this.rpViewController = loader.getController();
            this.rpViewController.initContext(this.rpStage);

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void show(){
        this.rpViewController.show();
    }

}
