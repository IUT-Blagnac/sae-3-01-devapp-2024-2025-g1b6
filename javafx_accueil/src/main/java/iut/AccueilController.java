package iut;

import java.io.IOException;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.ButtonType;


public class AccueilController {

    private Stage containingStage;

    @FXML
    private Button btnTesterCo;
    @FXML
    private Button btnLancer;
    @FXML
    private Button btnManuel;
    @FXML
    private Button btnCredits;
    @FXML
    private Button btnQuitter;

    @FXML
    private void switchToSecondary() throws IOException {
        App.setRoot("secondary");
    }



    public void initContext(Stage _containingStage) {
        this.containingStage = _containingStage;
        configure();
    }

    public void displayDialog() {
        containingStage.show();
    }

    private void configure(){
        this.btnTesterCo.setOnAction(event -> actionTesterCo());
        this.btnLancer.setOnAction(event -> actionLancer());
        this.btnManuel.setOnAction(event -> actionManuel());
        this.btnCredits.setOnAction(event -> actionCredits());
        this.btnQuitter.setOnAction(event -> actionQuitter());
        this.containingStage.setOnCloseRequest(event -> {
            event.consume();
            actionQuitter();
        });
        }

    @FXML
    private void actionQuitter() {
        Alert alert = new Alert(AlertType.CONFIRMATION, "Etes vous sur de vouloir quitter ?", ButtonType.YES, ButtonType.NO);
        alert.setTitle("Confirmation");
        alert.setHeaderText(null);

        alert.showAndWait().ifPresent(response -> {
            if (response == ButtonType.YES) {
            this.containingStage.close();
            }
        });
    }


    @FXML
    private void actionTesterCo() {
        System.out.println("Action tester connexion");
    }

    @FXML
    private void actionLancer() {
        System.out.println("Action lancer");
    }

    @FXML
    private void actionManuel() {
        System.out.println("Action manuel");
    }

    @FXML
    private void actionCredits() {
        System.out.println("Action credits");
    }


}
