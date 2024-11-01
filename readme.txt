=== Stein Mobile Switcher ===

Plugin Name: Stein Mobile Switcher
Version: 0.8.4
Stable tag: 0.8.4
Author URI: http://www.steinm.com
Plugin URI: http://www.steinm.com/blog/stein-mobile-switcher/
Description:  Switch themes by Device (Smartphone / Tablet).
Author: Matthias Stein
License: GPLv2
Text Domain: stein-mobile-switcher
Requires at least: 3.4.2
Tested up to: 3.4.2
Contributors: Matthias Stein
Tags: Mobile, Template, Theme, Switcher
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9Y2DV45N7DLGJ

The Stein Mobile Switcher detects which device (e.g. Smartphone, iPhone, Tablet) your visitor is using and enables a theme you specified.

== Description ==

The Stein Mobile Switcher detects which device (e.g. Smartphone, iPhone, Tablet) your visitor is using and enables a theme you specified. You may also display links to the different device-versions in your templates (see F.A.Q.). 



== Installation ==

= Upgrading From A Previous Version =

To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.


= Uploading The Plugin =

Extract all files from the ZIP file and then upload it to `/wp-content/plugins/`.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)


= Plugin Activation =

Go to the admin area of your WordPress install and click on the "Plugins" menu. Click on "Activate" for the "Stein Mobile Switcher" plugin.


= Plugin Usage =

In the admin area go to "Appearance -> Stein Mobile Switcher" and choose a template for each device.


== Frequently Asked Questions ==

= How can I add Links to the other device-versions in my templates?  =

Use the following functions to do that:

* sms_link_to_site($visibility = 'all', $target='full', $text = '') - Shows the "target"-Template


$visibility is the entry device of the visitor. Possible values are "all", "mobile", "iphone" and "tablet". E.g. if you want a link to the normal template to be visible only to smartphone users:
`<?php sms_link_to_site('mobile', 'full'); ?>`

You can also use OR:
`<?php sms_link_to_site('mobile|tablet|iphone', 'full'); ?>`

$target specifies the Target-Template. Possible values are:

1. tablet - Link to the Tablet-Site,
2. iphone - Link to the iPhone/iPod-Site,
3. mobile - Link to the Smartphone-Site,
4. full - Link to the normal Site

$text is the displayed link text.


Example usage:
`<?php
  if( function_exists('sms_link_to_site') ) { sms_link_to_site("mobile|tablet|iphone", "full", "Check out the full website!"); }
?>`

= How can I style the Links?  =

The links have a class equally to the functions name and target: class="sms_link_to_site_TARGET" (e.g. "sms_link_to_site_mobile"). Just style them with regular CSS.


== Screenshots ==

1. Admin area


== Changelog ==

= 0.8.4 =
* Bugfix for Links to "Full"-Site

= 0.8 =
* Added separate iPhone/iPod Detection
* Bugfix in Link-Visibility

= 0.7 =
* some smaller Bugfixes

= 0.6 =
* Added German Translation

= 0.5 =
* Initial Release


== Upgrade Notice ==

= 0.8 =
* Adds separate iPhone/iPod Detection: please activate a separate iPhone-Theme in the Plugin-Settings
* Bugfix in Link-Visibility

= 0.7 =
* fixes some small bugs

= 0.6 =
Adds German Translation