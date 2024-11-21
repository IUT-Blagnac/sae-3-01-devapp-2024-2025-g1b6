package iut.view;

import java.io.IOException;

import iut.App;
import javafx.fxml.FXML;

public class PrimaryController {

    @FXML
    private void switchToGraphiques() throws IOException {
        // Cette méthode va charger la scène des graphiques (graphiqueScene.fxml)
        App.setRoot("graphiques");
    }
}
