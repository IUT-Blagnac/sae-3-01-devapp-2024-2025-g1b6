package model.data;

import java.util.Map;

public class Measure {

    private final Map<String, Float> valuesMap;
    private final Map<String, String> infoMap;

    public Measure (Map<String, Float> pfValMap, Map<String, String> pfInfoMap, int pfFloor){
        this.valuesMap = pfValMap;
        this.infoMap = pfInfoMap;
    }

    public Map<String, Float> getValues(){
        return this.valuesMap;
    }

    public Map<String, String> getInfo(){
        return this.infoMap;
    }

}
