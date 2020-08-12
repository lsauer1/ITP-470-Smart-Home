#include "config.h"
// Initalize the feed connecting to AdafruitIO
AdafruitIO_Feed *feed = io.feed(FEED_NAME);

void setup() {
  //Start serial output
  Serial.begin(115200);
  while(! Serial);
  pinMode(4, OUTPUT);
  pinMode(16, OUTPUT);
  digitalWrite(4, LOW);
  digitalWrite(16, LOW);

  // Connect to IO system
  Serial.println("Connecting to IO");
  io.connect();

  // set up messageHandler to trigger on every update
  feed->onMessage(handleMessage);

  // Repeat until connected
  while(io.status() < AIO_CONNECTED) {
    Serial.print(".");
    delay(250);
  }

  // Update feed
  feed->get();

}

void loop() {
  // Processes feed updates
  io.run();
}

// This function is triggered on every feed update
// Output is the data recieved
void handleMessage(AdafruitIO_Data *data) {
  Serial.print("received <-  ");
  String valueRecieved = data->value();
  Serial.println(valueRecieved);
  if (valueRecieved.indexOf("ON") > -1) {
    Serial.println("true");
    digitalWrite(4, HIGH);
    delay(1000);
    digitalWrite(4, LOW);
    Serial.println("done");
  } else {
    Serial.println("false");
    digitalWrite(16, HIGH);
    delay(1000);
    digitalWrite(16, LOW);
    Serial.println("done");
    }
  }
