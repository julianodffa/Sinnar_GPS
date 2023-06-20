#include <WiFi.h>
#include <HTTPClient.h>
#include <TinyGPS++.h>
#include <SoftwareSerial.h>

SoftwareSerial serial_gps(17, 16);
TinyGPSPlus gps;

const char* ssid = "Juliano";
const char* password = "12345678";

float Longitude;
float Latitude;
float Speed;
//Your Domain name with URL path or IP address with path
String serverName = "http://192.168.34.3:8080/esp32/";

unsigned long currentMillis;

unsigned long GPSStartMillis;
unsigned long GPSPeriod = 5000;  //GPS period

unsigned long ServerSentMillis;
unsigned long ServerPeriod = 60000;

void setup() {
  Serial.begin(9600);
  serial_gps.begin(9600);

  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());

  Serial.println("Timer set to 1 Minutes (timerDelay variable), it will take 1 Minutes before publishing the Datas.");

  GPSStartMillis = millis();    //initial start time
  ServerSentMillis = millis();  //initial start time
}

void loop() {
  CheckGPS();
  currentMillis = millis();
  FunctionGPS();
  if (Longitude != NULL) {
    sentData();
  }
}

void sentData() {
  //Send an HTTP GET request every 1 minutes
  if (currentMillis - ServerSentMillis >= ServerPeriod) {
    //Check WiFi connection status
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;

      String serverPath = serverName + "?Longitude=" + String(Longitude, 6) + "&Latitude=" + String(Latitude, 6) + "&Speed=" + String(Speed, 2);

      http.begin(serverPath.c_str());

      // Send HTTP GET request
      int httpResponseCode = http.GET();

      if (httpResponseCode > 0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
        String payload = http.getString();
        Serial.println(payload);
      } else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      // Free resources
      http.end();
    } else {
      Serial.println("WiFi Disconnected");
    }
    ServerSentMillis = currentMillis;
  }
}

void CheckGPS() {
  while (serial_gps.available()) {
    gps.encode(serial_gps.read());
  }
}

void FunctionGPS() {
  if (currentMillis - GPSStartMillis >= GPSPeriod)  //test whether the period has elapsed
  {
    if (gps.location.isUpdated()) {
      Latitude = gps.location.lat();
      Longitude = gps.location.lng();
      Speed = gps.speed.kmph();
      Serial.println("");
      Serial.println("");

      Serial.println("Re-Checking GPS Signal");
      Serial.print("Latitude= ");
      Serial.println(Latitude, 6);

      Serial.print("Longitude= ");
      Serial.println(Longitude, 6);

      Serial.print("SPEED(KMPH)= ");
      Serial.println(Speed, 2);
    }
    GPSStartMillis = currentMillis;  //IMPORTANT to save the start time of the current LED state.
  }
}