module application.view.appjavaiot {
    requires javafx.controls;
    requires javafx.fxml;
    requires com.fasterxml.jackson.databind;


    opens application to javafx.fxml;
    exports application;
    exports application.view;
    opens application.view to javafx.fxml;
}