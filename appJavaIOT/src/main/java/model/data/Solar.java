package model.data;

import java.util.HashMap;
import java.util.Map;

/**
 * Représente un objet solaire, contenant des informations sur la production d'énergie des panneaux solaires.
 * <p>
 * Cette classe contient des données relatives à la production d'énergie sur différentes périodes (toute la durée de vie,
 * l'année dernière, le mois dernier, et le dernier jour). Elle inclut également la puissance actuelle produite par
 * les panneaux solaires.
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class Solar {

    /** Le moment de la dernière mise à jour des données. */
    private String lastUpdateTime;

    /** Une map contenant les données d'énergie sur différentes périodes. */
    private Map<String, Float> energyMap;

    /** La puissance actuelle produite par les panneaux solaires. */
    private float currentPower;

    /**
     * Constructeur de la classe Solar.
     * <p>
     * Ce constructeur initialise les données de production d'énergie pour différentes périodes et la puissance actuelle.
     *
     * @param pfLastUpdateTime Le moment de la dernière mise à jour des données.
     * @param pflifeTimeData La production totale d'énergie sur toute la durée de vie des panneaux solaires.
     * @param pflastYearData La production d'énergie de l'année dernière.
     * @param pflastMonthData La production d'énergie du mois dernier.
     * @param pflastDayData La production d'énergie du jour dernier.
     * @param pfCurrentPower La puissance actuelle produite par les panneaux solaires.
     */
    public Solar(String pfLastUpdateTime, float pflifeTimeData, float pflastYearData,
                 float pflastMonthData, float pflastDayData, float pfCurrentPower) {
        this.lastUpdateTime = pfLastUpdateTime;
        this.energyMap = new HashMap<>();
        this.energyMap.put("lifeTimeData", pflifeTimeData);
        this.energyMap.put("lastYearData", pflastYearData);
        this.energyMap.put("lastMonthData", pflastMonthData);
        this.energyMap.put("lastDayData", pflastDayData);
        this.currentPower = pfCurrentPower;
    }

    /**
     * Retourne le moment de la dernière mise à jour des données.
     *
     * @return Le moment de la dernière mise à jour des données sous forme de chaîne de caractères.
     */
    public String getLastUpdateTime() {
        return lastUpdateTime;
    }

    /**
     * Retourne la map contenant les données de production d'énergie sur différentes périodes.
     *
     * @return Une map avec les clés représentant les périodes ("lifeTimeData", "lastYearData", "lastMonthData", "lastDayData")
     *         et les valeurs correspondant à la production d'énergie pour chaque période.
     */
    public Map<String, Float> getEnergyMap() {
        return energyMap;
    }

    /**
     * Retourne la puissance actuelle produite par les panneaux solaires.
     *
     * @return La puissance actuelle en watts (float).
     */
    public float getCurrentPower() {
        return currentPower;
    }

    /**
     * Retourne la donnée d'énergie pour une période spécifiée.
     * <p>
     * Cette méthode permet d'obtenir la production d'énergie pour une période donnée (par exemple, toute la durée de vie,
     * l'année dernière, le mois dernier ou le dernier jour).
     *
     * @param key La clé correspondant à la période souhaitée (ex: "lifeTimeData", "lastYearData", "lastMonthData", "lastDayData").
     * @return La production d'énergie pour la période spécifiée.
     */
    public float getEnergyData(String key) {
        return energyMap.get(key);
    }
}
