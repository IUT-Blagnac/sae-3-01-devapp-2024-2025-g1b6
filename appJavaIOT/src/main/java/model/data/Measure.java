package model.data;

import java.util.Map;

public class Measure {

    private final Map<String, Object> valuesMap;
    private boolean alertMeasure;

    public Measure (Map<String, Object> pfValMap, boolean pfAlertMeasure){
        this.valuesMap = pfValMap;
        this.alertMeasure = pfAlertMeasure;
    }

    public Map<String, Object> getValues(){
        return this.valuesMap;
    }

}
