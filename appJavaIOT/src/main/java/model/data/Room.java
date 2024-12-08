package model.data;

import application.tools.SensorType;

import java.util.ArrayList;
import java.util.List;
import java.util.Set;

/**
 * Représente une salle avec des mesures collectées par des capteurs.
 * <p>Cette classe contient les informations sur le nom de la salle, l'étage, l'immeuble,
 * ainsi que les mesures effectuées par des capteurs dans cette salle. Les mesures sont
 * stockées dans une liste d'objets {@link Measure}.
 *
 * @see model.data.Measure
 * @see application.tools.SensorType
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class Room {

    /** Liste des mesures effectuées dans cette salle. */
    private List<Measure> roomValues;

    /** Liste des mesures qui déclenchent des alertes pour cette salle. */
    private List<Measure> roomAlertValues;

    /** Le nom de la salle. */
    private String roomName;

    /** L'étage où se trouve la salle. */
    private int floor;

    /** Le bâtiment auquel la salle appartient (représenté par un caractère). */
    private char building;

    /**
     * Constructeur de la classe Room.
     *
     * @param pfRoomName Le nom de la salle.
     * @param pfBuilding Le bâtiment auquel la salle appartient (représenté par un caractère).
     * @param pfFloor L'étage où la salle se trouve.
     */
    public Room(String pfRoomName, char pfBuilding, int pfFloor) {
        this.roomName = pfRoomName;
        this.building = pfBuilding;
        this.floor = pfFloor;

        this.roomValues = new ArrayList<>();
        this.roomAlertValues = new ArrayList<>();
    }

    /**
     * Retourne le nom de la salle.
     *
     * @return Le nom de la salle.
     */
    public String getRoomName() {
        return this.roomName;
    }

    /**
     * Retourne l'étage où se trouve la salle.
     *
     * @return L'étage de la salle.
     */
    public int getFloor() {
        return this.floor;
    }

    /**
     * Retourne le bâtiment auquel appartient la salle.
     *
     * @return Le bâtiment (représenté par un caractère).
     */
    public char getBuilding() {
        return this.building;
    }

    /**
     * Retourne la liste des mesures effectuées dans cette salle.
     *
     * @return La liste des mesures de la salle.
     */
    public List<Measure> getRoomValues() {
        return this.roomValues;
    }

    /**
     * Retourne la liste des mesures qui ont déclenché des alertes dans cette salle.
     *
     * @return La liste des mesures d'alerte de la salle.
     */
    public List<Measure> getRoomAlertValues() {
        return this.roomAlertValues;
    }

    /**
     * Retourne une chaîne de caractères représentant la salle et les dernières valeurs
     * des capteurs. La chaîne inclut le nom de la salle, ainsi que les dernières valeurs
     * de chaque capteur (type et unité).
     *
     * @return Une chaîne représentant la salle et ses dernières mesures.
     */
    @Override
    public String toString() {
        // Vérification si la liste de valeurs n'est pas vide pour éviter des erreurs
        if (this.roomValues.isEmpty()) {
            return this.roomName + " => Toutes les données dépassent le seuil d'alerte. Veuillez voir les logs d'alerte";
        }

        // Récupérer les types de capteurs de la dernière mesure
        Set<String> sensorTypes = this.roomValues.get(this.roomValues.size() - 1).getValues().keySet();
        String room = this.roomName;
        String lastValues = " =>";

        // Ajouter les valeurs des capteurs à la chaîne
        for (String key : sensorTypes) {
            lastValues += " " + SensorType.getByKey(key).getShortForm() + " : "
                    + this.roomValues.get(this.roomValues.size() - 1).getValues().get(key)
                    + SensorType.getByKey(key).getUnit();
        }

        // Retourner la chaîne finale en la combinant avec lastValues
        return room.concat(lastValues);
    }
}
