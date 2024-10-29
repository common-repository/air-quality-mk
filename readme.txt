=== Air Quality MK ===

Plugin Name:  Air Quality MK
Description:  This plugin show air quality in Macedonia. It provide widgets and shortcodes for displaying the quality of the air.
Plugin URI:   http://fa.mk/
Author:       Faton Mehmedi
Version:      0.1.1
Text Domain:  air-quality-mk
Domain Path:  /languages
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt

This plugin show air quality in Macedonia. It provide widgets and shortcodes for displaying the quality of the air.

== Description ==

Air Quality MK provides air quality data for Macedonia. It provide widgets and shortcodes for displaying the quality of the air.

The plugin uses an 3rd party API hosted on http://airquality.moepp.gov.mk. This API is used only to extract air quality data, it does not sent any data to the 3rd party API. The data are saved localy in the DB from the public API using cron schedule.

Features include:

* Widget that shows all parameter for one station, also you can change the station
* Create shortcodes for one station and one parameter with selected date and time (date and time is optional, if not specified the date is the last date)
* Include cron that dowload localy the data from API and store them in DB. The cron is schedule every 1 hour.

== Installation ==

= Installing manually: =

1. Unzip all files to the `/wp-content/plugins/air-quality-mk` directory
2. Log into WordPress admin and activate the 'Air Quality MK' plugin through the 'Plugins' menu
3. Go to *Air Quality* in the left-hand menu to start configuring the plugin


== Frequently Asked Questions ==

= How to create a shortcode? =

An example of shortcode is as follows [air_quality_mk_station_parameter_datetime station="Centar" parameter="PM10" datetime="20171228 12"]. This shortcode is with specifice date and time.
The date and time is optional as example: [air_quality_mk_station_parameter_datetime station="Centar" parameter="PM10D"]

== Screenshots ==

1. The settings page.
2. Widget example.
3. Shortcode example.

== Changelog ==
= 0.1.1 =
* Added city 'Gostivar'
= 0.1 =
* Initial version of the plugin

== Coming soon ==

These features are on our todo list. There's no particular timeframe for any of them and they're in no particular order:

* More widgets
* More shortcodes
* Localization in Macedonia language