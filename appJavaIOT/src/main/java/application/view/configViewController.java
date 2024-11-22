package application.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.RadioButton;
import javafx.scene.control.TextField;

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
    RadioButton btnOuiCapteur;

    @FXML
    RadioButton btnNonCapteur;

    @FXML
    RadioButton btnOuiPanneau;

    @FXML
    RadioButton btnNonPanneau;

}