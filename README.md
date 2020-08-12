# ITP 470: Smart Home
## How to Build Your Own Light Switch:

### Materials Needed:
#### 1.) Google Account
The website to connect and control the devices requires a google login to create an account.
#### 2.) 3D Printer
Currently the components will be entirely 3d printed, and as such a 3D printer with at least a 6x6" buildplate is required.
#### 3.) Included Parts:
In the files included with this project there is a bill of materials which includes the links to every required component along with their cost.
#### 4.) An Alexa Developer Account (Complex) or a IFTTT Account (Simple)
There are two ways to connect an alexa account to the arduino controlled devices, one is simple one which is more complicated. The complex method requires creating an alexa devloper account and copying the example files into a alexa skill using their developer console. There will be more instruction on this later, but a simple method exists that is reccomended. Using IFTTT it is possible to create alexa triggers that send HTTP requests to Adafruit IO, when sent the proper inputs it is capable of controling simple devices.

### Getting Started:
#### 1.) 3D Printing and Assembly
To begin one of every file must be printed (no supports are necessary). After that, 3MM heat-set inserts must be added to join the two base components together and the rails slotted in between them with the slider on them already. Next the motor mount must be screwed into the bottom mount and the motor attached. Finally the leadscrew must have a 3MM set screw inserted into the collar and must be slid over the motor shaft. A ziptie can then be used to secure the slider to the leadscrew and drive the slider.  Finally the two parts of the base can be screwed together. 
#### 2.) Wiring and Electronics:
There are three components: the arduino, the H-bridge based motor driver, and the DC motor. The motor driver must have wire leads soldered to its connectors and then have those cables run into the A motor ports on the motor driver. If down the line the device is driving the wrong way, it may be necesary to swap these two leads. Next both the arduino and motor driver will need to share a common 5 volts, this can be accompished by creating a power supply off of a USB cable and connecting its ground inputs to both the driver and the arduino. Next its positie five volt lead can be connected to the 5V pin on the arduino and the VCC pin on the motor driver. Finally pin A-1A and pin A-1B will be connected to pin GPIO4 and GPIO16 on the arduino respectively. This completes the wiring harness for the light switch controller.
#### 3.) Website Setup:
Navigate to the website where these devices are controlled (LINK), create an account if necessary then access the households tab. If no households exist, one must be created for this device to live in. After selecting a household you will be prompted to enter a device or feed name. Whatever you choose for this will be important to write the value down for later. The device is now created and setup for control through the website.
#### 4.) Arduino Code:
In this repository there is a arduino sketch that only requires minor modification to make work for any wifi network. In the config file of this sketch, it will ask for a wifi name, password, and feed name (the one entered into the website). After filling out these missing variables, download the code to your arduino and boot it up!
### Alexa Control:
