=== British Foreign Office Travel Advice ===
Contributors: mattaitch
Donate link: http://utopiamultimedia.com/foreign-office-travel-advice/
Tags: british foreign office, travel, advice, expedition, holiday, vacation, bigdata, big data, government
Requires at least: 3.8
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Displays the latest information via ajax or as the page loads, from the British Foreign Office on security and travel to other countries. 

== Description ==

British Foreign Office data includes information on safety and security, terrorism, local laws and customs, entry requirements, health and other aspects of travel for countries across the globe.

You can display the data in two ways:

* Via a button which pulls through the data via ajax

* Loads the data directly while loading the page

###Directions

1) Install the plugin

2) Create a custom meta tag called ‘geo_country’ to any post, adding the country you want information for. A full list of countries is available from the [BFO website](https://www.gov.uk/foreign-travel-advice).

3) There are two shortcodes you can use:
[fo-advice] – gets info as the page loads (displayed in an accordion type box)

[fo-advice-button] creates a button which when clicked, will retrieve and display the data via ajax.

### Demos
[Info loading as the page loads](http://utopiamultimedia.com/demo-british-foreign-office-advice-iraq/)

[Button and ajax loading](http://utopiamultimedia.com/demo-british-foreign-office-advice-romania/)

###Suggestions

Create a text widget with a title called ‘BFO Advice’
Drop the shortcode in the text area.

or 

Drop the code below into your widgets area of your code. It will only display a widget if the meta tag 'geo_country' is detected.

`<?php
/* look for a meta field called geo_country specific to the post displayed and if it's there, display a widget*/
$themeta = get_post_meta($pageID);
if (array_key_exists("geo_country",$themeta))
{
utopia_get_foreign_office();
};
function utopia_get_foreign_office(){
echo '<div class="widget-container">';
echo '<h3 class="widget-title">Foreign Office Advice</h3>';
echo do_shortcode('');
echo '</div>';
};
?>`

The way the information is formatted into elements is near the bottom of index.php (line 207 onwards). You can add a title by unremarking line 208. We’ve deliberately left the code with lots of space and relatively easy to understand so you can reformat how you see fit.

Beware! The elements and their classes / IDs are tied in to the accordion. For the accordion reference and options see [www.snyderplace.com](www.snyderplace.com)

###Donate

You can always [donate as a way of saying thanks!](http://utopiamultimedia.com/foreign-office-travel-advice/) 


== Installation ==


1. Upload `plugin-name.php` to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a custom meta tag in any post called 'geo_country'. Include the country as the meta tag value.
4. Use the shortcode [fo-advice] or [fo-advice-button]

== Screenshots ==

1. Create a custom meta tag in any post and add the country as the value
2. Once you've added the shortcode for pushing the information to a page on load up, this is what you'll see

== Changelog ==

= 1.0 =
* Initial release

== Additional Info ==

Thanks to [www.snyderplace.com](www.snyderplace.com) for the JQuery Accordion.


