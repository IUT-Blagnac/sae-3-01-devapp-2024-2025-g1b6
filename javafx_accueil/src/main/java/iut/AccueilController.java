package iut;

import java.io.IOException;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;

public class AccueilController {

    private Stage primaryStage;

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
}
