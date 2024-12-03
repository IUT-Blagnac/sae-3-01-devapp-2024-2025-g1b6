package application.model;

public class DeviceMetaData {
    private String deviceName;
    private String devEUI;
    private String room;
    private int floor;
    private String building;

    // Getters et Setters
    public String getDeviceName() { return deviceName; }
    public void setDeviceName(String deviceName) { this.deviceName = deviceName; }
    public String getDevEUI() { return devEUI; }
    public void setDevEUI(String devEUI) { this.devEUI = devEUI; }
    public String getRoom() { return room; }
    public void setRoom(String room) { this.room = room; }
    public int getFloor() { return floor; }
    public void setFloor(int floor) { this.floor = floor; }
    public String getBuilding() { return building; }
    public void setBuilding(String building) { this.building = building; }
}
