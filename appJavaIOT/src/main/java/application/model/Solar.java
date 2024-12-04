package application.model;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class Solar {

    private String lastUpdateTime;
    private Map<String, Float> energyMap;
    private float currentPower;


    public Solar (String pfLastUpdateTime, float pflifeTimeData, float pflastYearData,
                  float pflastMonthData, float pflastDayData, float pfCurrentPower){
        this.lastUpdateTime= pfLastUpdateTime;
        this.energyMap = new HashMap<>();
        this.energyMap.put("lifeTimeData", pflifeTimeData);
        this.energyMap.put("lastYearData", pflastYearData);
        this.energyMap.put("lastMonthData", pflastMonthData);
        this.energyMap.put("lastDayData", pflastDayData);
        this.currentPower = pfCurrentPower;

    }

    // Getter for lastUpdateTime
    public String getLastUpdateTime() {
        return lastUpdateTime;
    }

    // Getter for energyMap
    public Map<String, Float> getEnergyMap() {
        return energyMap;
    }

    // Getter for currentPower
    public float getCurrentPower() {
        return currentPower;
    }

    public float getEnergyData(String key) {
        return energyMap.get(key);
    }

}