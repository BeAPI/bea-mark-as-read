# BEA - Mark as read

This plugin lets you know which user has read an article from your WordPress site. It also makes it possible to display this information (percentage of reading), a practical thing for intranet type use.

A shortcode is available by default to display the list of people who have read, and those who have not read the article. The syntax is ``[bea-mas]``

The plugin stores this information in a specific table, in order to generate statistics or reminders.

This is an AJAX JavaScript call that determines whether the article has been read or not. We use the Waypoint library to determine if the user has read the article in its entirety.

This plugin has been developed with the latest version of WordPress (4.9.x) and requires PHP 7.x

## Installation

It's like a classic plugin to install and activate :)
At this stage, no information is added to the back office.

## For developers

An example is available in the plugin to automatically add this information to the end of the post content.
/samples/