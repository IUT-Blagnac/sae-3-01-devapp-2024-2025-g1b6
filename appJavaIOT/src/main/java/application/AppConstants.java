package application;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

/**
 * Cette classe contient les constantes utilisées dans l'application.
 * Elle centralise les informations qui ne changent pas, comme la liste
 * des salles disponibles pour les capteurs.
 * 
 * @author Alex LOVIN
 */
public class AppConstants {

    /**
     * Liste observable des salles disponibles pour les capteurs.
     * Cette liste est utilisée dans plusieurs parties de l'application
     * pour offrir des options prédéfinies.
     * @author Alex LOVIN
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

    // Constructeur privé pour empêcher l'instanciation de la classe
    private AppConstants() {
        throw new UnsupportedOperationException("Cette classe ne doit pas être instanciée.");
    }
}
