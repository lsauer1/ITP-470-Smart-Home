# ITP 470: Smart Home
## Description:
This is a smart home project for a USC ITP 470 course. This project was focused on the creation of simple Arduino based smart home devices. It includes a website for remote control of devices and sharing of households through google accounts, a remote-controlled light switch add-on, and methods for integrating commands with Alexa. To create these components knowledge on SQL databases, Arduinos, electrical circuits, DC motor control, Alexa skills, and website design was required.

## How to Build Your Own Light Switch:
### Materials Needed:
#### 1.) Google Account
The website to connect and control the devices requires a google login to create an account.
#### 2.) 3D Printer
Currently the components will be entirely 3d printed, and as such a 3D printer with at least a 6x6" build plate is required.
#### 3.) Included Parts:
In the files included with this project there is a [bill of materials](BOM.xlsx) which includes the links to every required component along with their cost.
#### 4.) An Alexa Developer Account (Complex) or a IFTTT Account (Simple)
There are two ways to connect an Alexa account to the Arduino controlled devices, one is simple one which is more complicated. The complex method requires creating an Alexa developer account and copying the example files into an Alexa skill using their developer console. There will be more instruction on this later, but a simple method exists that is recommended. Using IFTTT it is possible to create Alexa triggers that send HTTP requests to Adafruit IO, when sent the proper inputs it is capable of controlling simple devices.

### Getting Started:
#### 1.) 3D Printing and Assembly
To begin one of every file must be printed (no supports are necessary). After that, 3MM heat-set inserts must be added to join the two base components together and the rails slotted in between them with the slider on them already. Next the motor mount must be screwed into the bottom mount and the motor attached. The leadscrew must have a 3MM set screw inserted into the collar and must be slid over the motor shaft and a bearing supports it at the other end. A zip tie can then be used to secure the slider to the leadscrew and drive the slider.  Finally, the two parts of the base can be screwed together. 
#### 2.) Wiring and Electronics:
There are three components: the Arduino, the H-bridge based motor driver, and the DC motor. The motor driver must have wire leads soldered to its connectors and then have those cables run into the A motor ports on the motor driver.
<p align="center">
![Image of Motor A Receptacles](vccPin.jpg)
*Motor A receptacles on the motor driver*
 </p>
If down the line the device is driving the wrong way, it may be necessary to swap these two leads. Next both the Arduino and motor driver will need to share a common ground, this can be accomplished by creating a power supply off of a USB cable and connecting its ground inputs to both the driver and the Arduino. Next its positive 5 volt lead can be connected to the 5V pin on the Arduino and the VCC pin on the motor driver.
<p align="center">
![Image of VCC and GND Pins](vccPin.jpg)
*VCC and GND pins on the motor driver*
 </p>
Finally, pin A-1A and pin A-1B will be connected to pin GPIO4 and GPIO16 on the Arduino respectively.
<p align="center">
![Image of Motor A Control Pins](vccPin.jpg)
*Motor A control pins on the motor driver*
 </p>
This completes the wiring harness for the light switch controller.
#### 3.) Website Setup:
Navigate to the website where these devices are controlled [LINK](https://itp-smart-home.herokuapp.com/index.php), create an account if necessary then access the households tab. If no households exist, one must be created for this device to live in. After selecting a household you will be prompted to enter a device or feed name. Whatever you choose for this will be important to write the value down for later. The device is now created and setup for control through the website.
#### 4.) Arduino Code:
In this repository there is an Arduino sketch that only requires minor modification to make work for any Wi-Fi network. In the config file of this sketch, it will ask for a Wi-Fi name, password, and feed name (the one entered into the website). After filling out these missing variables, download the code to your Arduino and boot it up!
### Alexa Control:
As mentioned before there are two methods of Alexa connectivity that can be used. The recommended method is to use IFTTT to setup a skill that triggers an Adafruit IO event based on an Alexa trigger. However, for more developer access, the code used to connect to Alexa through the developer console is also included.
#### 1.) IFTTT (simple)
To begin create an account on [IFTTT](https://www.IFTTT.com) and naviagte to the create a skill page. On this page you can input a number of modules in an **if this** than **that** format. To begin set the **if** trigger to be alexa focused, and set a trigger phrase that you want for this device (e.x. switch one on). Then naviagte to the **then** option and search for the Adafruit Io plug in. After selecting this you will want to select the appropriate feed name and then set the message. TO turn a switch on it should contain a message containing **ON** and to turn it off it should send a message conatining **OFF**.
#### 2.) Alexa Developer Console (challenging)
This method is extremely complicated and should only really be attempted for developer purposes and/or if you have previous experience in the Alexa skills framework. This method allows for direct control of HTTP requests to the Adafruit IO client to update devices. This can be accomplished by generating a HelloWorld skill example in the Alexa Developer console. From there the main code will be swapped for the javascript file included in the Alexa section of the repository. Then the invocation name should be changed to something that mirrors the concept of the program (e.x. smart home). After that, the intents (found in the build tab) can be altered to be whatever the user wants to trigger the device activation. Finally a slot should be created called value for setting the brightness of the light is entered.

## Looking to the Future:
### Reflection:
This device was simple in comparison to many of the devices that this system could be expanded to include. However, the hard backend portions of this system will easily transfer to other devices. By utilizing my own website to control them and using HTTP requests to integrate with Alexa, methods were created for interactions with nearly any device. The skills that I learned in the development of this device will enable me to expand this project in the future!
### Accessibility Devices:
After the creation of this light switch, which was a proof of concept of this system, I hope to expand more into the accessibility market. These days it seems that many seniors are adopting smart home systems to help them in their daily lives. My next device would capitalize on this by creating a simple Raspberry Pi based system capable of reading oven and microwave displays and reporting the message through Alexa. Further devices could include tags that trigger if you are leaving something behind in your house, or even an Alexa integrated pill dispenser with automatic reminders.
### Lessons Learned:
I overcomplicated a lot of this project at first, I wanted to use a complex stepper motor driver with speed control to trigger light switches. It took me a while to step back and really think about the goals of that mechanism and realize that a simple solution would just be a DC motor with a position sensor. This mentality of KISS (Keep It Simple Silly) is something that I found valuable to apply throughout this project and ultimately helped me finish despite some very unlucky circumstances.
