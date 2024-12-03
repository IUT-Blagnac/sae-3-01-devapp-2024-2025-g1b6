package model.data;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

public class Room {

    private List<Measure> roomValues;
    private String roomName;
    private int floor;
    private char building;

    public Room(String pfRoomName, char pfBuilding, int pfFloor){
        this.roomName = pfRoomName;
        this.building = pfBuilding;
        this.floor = pfFloor;

        this.roomValues = new ArrayList<>();
    }

    public String getRoomName(){
        return this.roomName;
    }

    public int getFloor(){
        return this.floor;
    }

    public char getBuilding(){
        return this.building;
    }

    public List<Measure> getRoomValues() {
        return this.roomValues;
    }
}
