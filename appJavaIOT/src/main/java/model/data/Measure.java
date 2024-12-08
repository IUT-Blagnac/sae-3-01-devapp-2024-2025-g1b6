package model.data;

import application.tools.SensorType;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.Locale;
import java.util.Map;
import java.util.Set;

/**
 * Représente une mesure effectuée par un capteur à un instant donné.
 *
 * <p>Cette classe encapsule une mesure comprenant un ensemble de valeurs associées à des types de capteurs spécifiques.
 * Elle stocke ces valeurs dans une carte ({@link Map}), avec la possibilité de vérifier les informations relatives à chaque capteur,
 * telles que l'unité de mesure et l'abréviation associée.
 * <p>De plus, cette classe contient une date d'enregistrement indiquant le moment où la mesure a été effectuée, et elle permet de formater
 * les informations de manière lisible pour une sortie textuelle.
 *
 * @see java.util.Map
 * @see SensorType
 * @author Yassir Boulouiha Gnaoui
 */
public class Measure {

    /** La map des valeurs de la mesure, où chaque clé correspond à un type de capteur et chaque valeur à la mesure prise. */
    private final Map<String, Object> valuesMap;

    /** La date et l'heure auxquelles la mesure a été enregistrée. */
    private final LocalDateTime registerDate;

    /**
     * Constructeur de la classe {@link Measure}.
     * <p>
     * Ce constructeur initialise une instance de {@link Measure} en associant les valeurs des capteurs à une carte et en fixant
     * la date d'enregistrement de la mesure.
     *
     * @param pfValMap La map contenant les valeurs de la mesure, où chaque clé représente un type de capteur et chaque valeur
     *                 représente la donnée mesurée par le capteur.
     * @param date     La date et l'heure d'enregistrement de la mesure.
     * @throws NullPointerException si l'une des valeurs du paramètre {@code pfValMap} ou {@code date} est nulle.
     */
    public Measure(Map<String, Object> pfValMap, LocalDateTime date) {
        this.valuesMap = pfValMap;
        this.registerDate = date;
    }

    /**
     * Retourne la map des valeurs de la mesure.
     * <p>
     * Cette méthode permet d'accéder aux valeurs enregistrées par les capteurs sous forme de {@link Map}. Chaque clé
     * dans la map correspond à un type de capteur et chaque valeur correspond à la donnée mesurée par ce capteur.
     *
     * @return La map des valeurs de la mesure.
     */
    public Map<String, Object> getValues() {
        return this.valuesMap;
    }

    /**
     * Retourne une représentation textuelle complète de la mesure.
     * <p>
     * Cette méthode génère une chaîne de caractères qui contient la date et l'heure d'enregistrement de la mesure,
     * suivie des valeurs mesurées par les capteurs. Les informations suivantes sont incluses pour chaque capteur :
     * <ul>
     *     <li>L'abréviation du type de capteur.</li>
     *     <li>La valeur mesurée par le capteur.</li>
     *     <li>L'unité associée à la mesure.</li>
     * </ul>
     * Le format de la date utilisée est "dd/MM/yyyy HH:mm:ss" pour garantir une lisibilité optimale.
     *
     * @return La représentation textuelle complète de la mesure, incluant la date et les valeurs des capteurs.
     */
    @Override
    public String toString() {
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd/MM/yyyy HH:mm:ss", Locale.FRANCE);
        String res = this.registerDate.format(formatter);

        // Récupérer les types de capteurs de la dernière mesure
        Set<String> sensorTypes = this.valuesMap.keySet();

        // Ajouter les valeurs des capteurs à la chaîne de résultat
        for (String key : sensorTypes) {
            res +=   " => " + SensorType.getByKey(key).getShortForm() + " : "
                    + getValues().get(key)
                    + SensorType.getByKey(key).getUnit();
        }
        return res;
    }
}
