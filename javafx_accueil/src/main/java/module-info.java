module iut {
    requires javafx.controls;
    requires javafx.fxml;

    opens iut to javafx.fxml;
    exports iut;
}
