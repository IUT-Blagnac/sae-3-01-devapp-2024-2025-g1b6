module application.view.appjavaiot {
    requires javafx.controls;
    requires javafx.fxml;
    requires com.fasterxml.jackson.databind;
    requires java.desktop;
    requires ini4j;


    opens application to javafx.fxml;
    exports application;
    exports application.view;
    opens application.view to javafx.fxml;
    exports application.config;
    opens application.config to javafx.fxml;
}