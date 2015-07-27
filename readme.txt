=== Salon booking system ===
Contributors: wordpresschef
Tags: booking, reservations, barber shop, hair salon, beauty center, spas, scheduling, appointment
Requires at least: 4.1
Tested up to: 4.2.2
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Salon booking creates a complete and easy to manage appointment booking system inside your wordPress installation.


== Description ==

Salon booking, is a plugin for WordPress based websites that creates a complete and easy to manage booking system in order to give your customers the ability to book for one or more services on your website.

Salon booking  is the best solution for: 

* Hair dresser salons
* Barber shop
* Beauty salons
* Spas 

and all that kind of businesses that want to offer a quality online booking service to their clients.

Salon booking works upon a double booking algorithm:

* Basic - fixed booking duration
* Advanced - booking duration is based on the sum of the services booked

Salon booking is provided with a intuitive back-end bookings calendar where the administrator can have a quick overview of all the upcoming reservations.



**LIST OF THE FEATURES**


BRAND NEW: 

*Advanced booking algorytm - calculate the sum of the duration of the services booked

*Hide prices option - if you don't want to show-up services prices

**EXISTING FEATURES**:

* Ajax loading option

* Assistant selection

If you want you can give your customers the possibility to choose their favourite assistant during the booking process.


* Assistant e-mail notification

When a new booking is made the selected assistant will be notified by e-mail


* SMS user verification

Avoid spam and verify your customers identity using an SMS verification process during the first time registration. The SMS verification process supports TWILIO, PLIVO and IP1SMS providers 


* Staff user role

You can give access to the back-end to your salon’s staff limiting them to manage only the booking and calendar settings pages using the custom “Salon staff” user role. 


* Intuitive bookings calendar

Administrator has a full overview of the upcoming bookings for every single day of the month. Clicking on a single calendar day a list with all reservations will be displayed. Every single booking is linked to its own details page.


* Booking rules

Administrator can set how many people can book for the same date/time and how long last on average a single slot (time/session). This setting should represent the capacity of your salon of attending people one or more people at same time.


* Set your salon timetable

Administrator can set the open and closing days and the time slots using our multiple rules system. This will be reflected on front-end bookings calendar.


* Accept online Payments
Salon booking is ready to accept online payments with Paypal. You can decide if user can pay in advance using credit cards or “pay later” once arrived at the salon.


* Create as many services as you want
Easily create as many services as you need, edit or delete them as a simple post.


* Full control of every single service
Set  its price, duration, days of non availability, and a brief description. You can even make a difference between “main” and “optional” services.


* Full control of every single booking
When you receive a reservation you can view and eventually edit its own details. You can easily Add a new booking manually as you should for a post.


* Nice emails confirmation
When you receive a new reservation an email will be sent to you and to your client with all booking details.


More information at [Salon booking](http://salon.wpchef.it/).


== Installation ==

This section describes how to install the plugin and get it working.


1. Upload `salon-free` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Salon > Settings to complete your salon’s settings



== Frequently Asked Questions ==


= What kind of business this plugins best fits? =

This plugin has been developed thinking about the specific need of Barber Shop, Hairdressing salon, Beauty Centers and Spas.

 
= Which version of WordPress this plugin requires? =

The plugin has been tested on WordPress 4.0

 
= Which version of php is supported? =

The plugin supports php 5.3 and above version.

 
= Is it possible to accept online payments? =

Yes. At the moment only PayPal as payment gateway is supported.

Do you need a custom payment gateway? Please contact us.

 

= Is it multi language ready? =

The plugin can be translated in any languages  creating a .po file or using WPML plugin translating the strings.

NOTE: Put your own translation files inside wp-content/languages/plugins

If you want to contribuite to plugin translation please visit:

https://www.transifex.com/projects/p/salon-booking-system/

Languages available:

*English
*German
*French
*Italian

 
= Are there any conflicts with other plugins? =

At the moment we didn’t spot any conflicts with other plugins.

 
= Is it possible to customise the look and feel of the plugin front-end? =

Every front-end element of the booking process has its own css class, so you can easily customise it.


= What is the limit of the free version? =

You can accept up to 30 reservations

= What happen when plugin free version reach its booking limit? =

You should buy the PRO version here:

http://plugins.wpchef.it/downloads/salon-booking-wordpress-plugin/


== Screenshots ==

1. screenshot-1.gif salon general settings

2. screenshot-2.gif service’s details

3. screenshot-3.gif  booking’s details

4. screenshot-4.gif salon’s booking rules

5. screenshot-5.gif payments settings

6. screenshot-6.gif attendants section

7. screenshot-7.gif front-end booking process demo


== Changelog ==


1.0.6 27/07/2015

* fixed bug on date picker (first week not bookable)
* fixed css compatibility with twitter bootstrap based theme 


1.0.5 07/07/2015

* fixed WPML compatibily issue 
* fixed real time availabilty control on date/time picker
* fixed booking range selection issue
* fixed time-session average duration bug


1.0.4 02/06/2015

* Ajax loading option
* Currency position option
* New Address field
* Time selection fix
* Date picker fixes for Dutch and Norway languages
* Missing translations strings
* Modified many english text strings


1.0.3 22/05/2015

Date-picker multilanguage support fix


1.0.2 19/05/2015

Date-picker multilanguage support fix

1.0.1 13/05/2015

* Added “Assistant selection" option
* Added "SMS Verification" option
* Add "Salon staff" new users role
* Fixed booking system bug
