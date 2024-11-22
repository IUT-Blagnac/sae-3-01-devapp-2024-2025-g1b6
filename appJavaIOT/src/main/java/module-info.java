module application.view.appjavaiot {
    requires javafx.controls;
    requires javafx.fxml;


    opens application to javafx.fxml;
    exports application;
    exports application.view;
    opens application.view to javafx.fxml;
}