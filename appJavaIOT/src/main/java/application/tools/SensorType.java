package application.tools;

import java.util.HashMap;
import java.util.Map;

/**
 * Enumération représentant différents types de capteurs avec leur diminutif et unité de mesure.
 *
 * <p>Cette enum permet de gérer les types de capteurs utilisés dans l'application, en fournissant un accès rapide
 * aux diminutifs et unités de mesure correspondants. Une map statique est utilisée pour permettre une recherche
 * rapide par clé.
 *
 * @see java.util.HashMap
 * @see java.util.Map
 * @see Enum
 *
 * @author Yassir Boulouiha Gnaoui
 */
public enum SensorType {
    CO2("co2", "ppm"),
    TEMPERATURE("temp", "°C"),
    HUMIDITY("hum", "%"),
    ACTIVITY("act", "count"),
    TVOC("tvoc", "ppb"),
    ILLUMINATION("illu", "lux"),
    INFRARED("ir", "°C"),
    INFRARED_AND_VISIBLE("ir_vis", "lux"),
    PRESSURE("pres", "hPa");

    private final String shortForm;
    private final String unit;

    // Map pour la recherche rapide par clé
    private static final Map<String, SensorType> lookup = new HashMap<>();

    // Initialisation de la map statique
    static {
        for (SensorType type : SensorType.values()) {
            lookup.put(type.name().toLowerCase(), type);
        }
    }

    /**
     * Constructeur pour initialiser le diminutif et l'unité de mesure.
     *
     * @param shortForm Le diminutif du type de capteur.
     * @param unit L'unité de mesure du type de capteur.
     */
    SensorType(String shortForm, String unit) {
        this.shortForm = shortForm;
        this.unit = unit;
    }

    /**
     * Obtient le diminutif du type de capteur.
     *
     * @return Le diminutif du type de capteur.
     */
    public String getShortForm() {
        return shortForm;
    }

    /**
     * Obtient l'unité de mesure du type de capteur.
     *
     * @return L'unité de mesure du type de capteur.
     */
    public String getUnit() {
        return unit;
    }

    /**
     * Recherche un type de capteur par clé.
     *
     * @param key La clé du type de capteur à rechercher.
     * @return Le type de capteur correspondant à la clé, ou {@code null} si aucune correspondance n'est trouvée.
     * @see java.util.Map#get(Object)
     */
    public static SensorType getByKey(String key) {
        return lookup.get(key.toLowerCase());
    }
}