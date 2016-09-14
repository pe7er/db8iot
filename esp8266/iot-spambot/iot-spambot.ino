 /**
 * Helloworld style, connect an ESP8266 to the IBM IoT Foundation
 * 
 * Author: Ant Elder
 * License: Apache License v2
 */
#include <ESP8266WiFi.h>
#include <PubSubClient.h> // https://github.com/knolleary/pubsubclient/releases/tag/v2.3

//-------- Customise these values -----------
const char* ssid = "Skynet";
const char* password = "";

#define ORG "IoT Spam"
#define DEVICE_TYPE "ESP8266"
#define DEVICE_ID "Spamming"
#define TOKEN "<yourToken>"
//-------- Customise the above values --------

char server[] = "192.168.3.1";
char topic[] = "test";
char authMethod[] = "";//use-token-auth
char token[] = TOKEN;
char clientId[] = "d:" ORG ":" DEVICE_TYPE ":" DEVICE_ID;

long randNumber;

WiFiClient wifiClient;
PubSubClient client(server, 1883, NULL, wifiClient);

void setup() {
 Serial.begin(115200);
 Serial.println();

 Serial.print("Connecting to "); Serial.print(ssid);
 WiFi.begin(ssid, password);
 while (WiFi.status() != WL_CONNECTED) {
 delay(500);
 Serial.print(".");
 } 
 Serial.println("");

 Serial.print("WiFi connected, IP address: "); Serial.println(WiFi.localIP());

  // if analog input pin 0 is unconnected, random analog
  // noise will cause the call to randomSeed() to generate
  // different seed numbers each time the sketch runs.
  // randomSeed() will then shuffle the random function.
  randomSeed(analogRead(0));
}

int counter = 0;

void loop() {

 if (!!!client.connected()) {
 Serial.print("Reconnecting client to ");
 Serial.println(server);
 while (!!!client.connect(clientId, authMethod, token)) {
 Serial.print(".");
 delay(500);
 }
 Serial.println();
 }

String spams[] = {
"A simple sales copy formula",
"Does your marketing smell funny?",
"Two Words: Cheap Traffic!",
"Fashion Sunglasses Now in Stock",
"You can make 250 USD per day!",
"Easy way to make and get dollars online",
"Rainy days will never come if you earn approximately 8,000 per month!",
"How to craft a guarantee that sells",
"Gold In Your Mailbox",
"[NEW FORMULA] Cheap, Targeted Facebook Traffic",
"198% ROI on Twitter Ads",
"Do NOT sell on Amazon without this 10 dollar tool",
"My business model on a napkin?",
"321% higher conversions using THIS",
"Amazon app cherry-picks hottest products for you",
"Do you HATE money?",
"[ONLY 7 Euro] My cheap traffic plan",
"28,507 leads in 45 days",
"Copy + paste this 10 million business",
"Boost your email clickthroughs by 200%",
"Good News Your 1st sale in 3 days",
"How I get dirt-cheap, high-quality traffic",
"Can I help you build your sales funnel?",
"THIS Increased Conversions 24%?!?"
};

 // print a random number from 10 to 19
 //randNumber = random(10, 20);
 randNumber = random(1, sizeof(spams));  
 
 String payload = spams[randNumber];


 //String payload = "{\"d\":{\"myName\":\"ESP8266.Test1\",\"counter\":";
 //payload += counter;
 //payload += "}}";
 
 Serial.print("Sending payload: ");
 Serial.println(payload);
 
 if (client.publish(topic, (char*) payload.c_str())) {
 Serial.println("Publish ok");
 } else {
 Serial.println("Publish failed");
 }

 ++counter;
 delay(50);
}
