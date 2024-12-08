package application.config;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

/**
 * Classe contenant les constantes de l'application.
 * Elle fournit des listes prédéfinies pour les salles et les types de données des capteurs.
 * @author Alex LOVIN
 */
public class AppConstants {
    /**
     * Liste des noms des salles où les capteurs sont installés.
     */
    public static final ObservableList<String> SALLES_CAPTEURS = FXCollections.observableArrayList(
        "B112", "C002", "B217", "E001", "B108", "C102", "E007", "amphi1",
        "B203", "E208", "E210", "E207", "B103", "E101", "C006", "E100",
        "hall-amphi", "hall-entrée-principale", "E103", "E102", "B110",
        "B106", "B001", "E004", "E106", "Local-velo", "B202", "C004",
        "Foyer-personnels", "B201", "B109", "C001", "B002", "Salle-conseil",
        "B105", "Foyer-etudiants-entrée", "C101", "B111", "B113", "E006",
        "E104", "E209", "E003"
    );
    /**
     * Liste des types de données collectées par les capteurs.
     */
    public static final ObservableList<String> DATA_TYPE = FXCollections.observableArrayList(
            "co2", "temperature", "humidity", "activity",
            "tvoc", "illumination", "infrared", "infrared_and_visible", "pressure"
    );
}
