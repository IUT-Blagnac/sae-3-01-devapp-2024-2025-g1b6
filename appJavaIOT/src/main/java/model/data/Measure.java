package model.data;

import java.util.Map;

/**
 * Représente une mesure effectuée par un capteur.
 *
 * <p>Cette classe contient un ensemble de valeurs associées à une mesure, stockées sous forme de {@link Map}.
 * Elle inclut également une information pour savoir si la mesure dépasse un seuil d'alerte.
 *
 * @see java.util.Map
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class Measure {

    private final Map<String, Object> valuesMap;
    private boolean alertMeasure;

    /**
     * Constructeur de la classe Measure.
     *
     * @param pfValMap La map des valeurs associées à cette mesure.
     * @param pfAlertMeasure Indique si cette mesure déclenche une alerte.
     */
    public Measure(Map<String, Object> pfValMap, boolean pfAlertMeasure) {
        this.valuesMap = pfValMap;
        this.alertMeasure = pfAlertMeasure;
    }

    /**
     * Retourne la map des valeurs de cette mesure.
     *
     * @return La map des valeurs de la mesure.
     */
    public Map<String, Object> getValues() {
        return this.valuesMap;
    }

}
