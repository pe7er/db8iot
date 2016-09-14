// Arduino program to toggle 2 LEDs
//
// Listen to ALL # Topics
//
// GREEN LED
// VCC + GPIO0
// green_blink
//
// RED LED
// VCC + GPIO2
// red_on
// red_off
//
/// source: http://www.esp8266.com/viewtopic.php?f=29&t=2745 ///

#include <PubSubClient.h>
#include <ESP8266WiFi.h>

const char* ssid = "Skynet";
const char* password = "";
char* topic = "#";     //  using wildcard to monitor all traffic from mqtt server
char* server = "192.168.3.1";  // Address of my server on my network, substitute yours!
 
char message_buff[100];   // initialise storage buffer (i haven't tested to this capacity.)

void callback(char* topic, byte* payload, unsigned int length) {
  // Here is what i have been using to handle subscriptions. I took it as a snippet from elsewhere but i cannot credit author as i dont have reference!
    int i = 0;

  Serial.println("Message arrived:  topic: " + String(topic));
  Serial.println("Length: " + String(length,DEC));
  
  // create character buffer with ending null terminator (string)
  for(i=0; i<length; i++) {
    message_buff[i] = payload[i];
  }
  message_buff[i] = '\0';
  
  String msgString = String(message_buff);
  
  Serial.println("Payload: " + msgString);
  int state = digitalRead(2);  // get the current state of GPIO1 pin
  //if (msgString == "alarm"){    // if there is a "1" published to any topic (#) on the broker then:
  //  digitalWrite(2, !state);     // set pin to the opposite state 
  //  Serial.println("Switching LED"); 
  //}
  
  // if there is a "alarm_on" published to any topic (#) on the broker then:  
  if (msgString == "red_on"){    
    digitalWrite(2, 0);   
    Serial.println("Switching LED ON"); 
  }
  if (msgString == "red_off"){
    digitalWrite(2, 1);   
    Serial.println("Switching LED OFF"); 
  }
    if (msgString == "green_blink"){    
    digitalWrite(0, 0);   
    Serial.println("Switching LED ON"); 
    delay(500);
    digitalWrite(0, 1);   
    Serial.println("Switching LED OFF");    
  }
}
 
WiFiClient wifiClient;
PubSubClient client(server, 1883, callback, wifiClient);
 
 
String macToStr(const uint8_t* mac)
{
  String result;
  for (int i = 0; i < 6; ++i) {
    result += String(mac[i], 16);
    if (i < 5)
      result += ':';
  }
  return result;
}
 
void setup() {
  Serial.begin(115200);
  delay(10);
  
    // prepare GPIO2 *********************************************************************
  pinMode(2, OUTPUT);   // i am using gpio2 as output to toggle an LED
  digitalWrite(2, 0);  //*****************************************************************

  // prepare GPIO0 *********************************************************************
  pinMode(0, OUTPUT);   // gpio0 as output for GREEN LED
  digitalWrite(0, 0);  //*****************************************************************

  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected");  
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
 
  //  connection to broker script.
  if (client.connect("arduinoClient")) {
    client.publish("test","hello, I am an alarm LED for JandBeyond");
    client.subscribe(topic);
  }

// Switch OFF both LEDs after startup
  digitalWrite(0, 1);
  digitalWrite(2, 1);
}
 
void loop() {

  client.loop();
}



