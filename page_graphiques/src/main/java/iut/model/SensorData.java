package iut.model;

public class SensorData {
    private double temperature;
    private double humidity;
    private int activity;
    private int co2;
    private int tvoc;
    private int illumination;
    private int infrared;
    private int infrared_and_visible;
    private double pressure;

    // Getters et Setters
    public double getTemperature() { return temperature; }
    public void setTemperature(double temperature) { this.temperature = temperature; }
    public double getHumidity() { return humidity; }
    public void setHumidity(double humidity) { this.humidity = humidity; }
    public int getActivity() { return activity; }
    public void setActivity(int activity) { this.activity = activity; }
    public int getCo2() { return co2; }
    public void setCo2(int co2) { this.co2 = co2; }
    public int getTvoc() { return tvoc; }
    public void setTvoc(int tvoc) { this.tvoc = tvoc; }
    public int getIllumination() { return illumination; }
    public void setIllumination(int illumination) { this.illumination = illumination; }
    public int getInfrared() { return infrared; }
    public void setInfrared(int infrared) { this.infrared = infrared; }
    public int getInfrared_and_visible() { return infrared_and_visible; }
    public void setInfrared_and_visible(int infrared_and_visible) { this.infrared_and_visible = infrared_and_visible; }
    public double getPressure() { return pressure; }
    public void setPressure(double pressure) { this.pressure = pressure; }
}
