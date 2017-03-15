# eawo-profile-widget-dockerhub
A WordPress widget that utilises the Docker Hub API to display a snapshot of your Docker Hub profile on your WordPress blog.

# Plugin Details
Website: https://eamonwoortman.nl/dockerhub-profile-wordpress-widget/
Tags: dockerhub, widget, profile, code
Requires WP version: 3.0.1
Tested up to WP version: 4.7.2
Stable tag: 1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

# Description

Got an interesting Docker Hub profile you want to share with the world? 

Simply add the widget to your preferred location and enter your Docker Hub username.
The widget plugin features include:

* Stylized widget inspired by the Docker Hub website
* Showing the users avatar
* Showing some user information like;
 * Username
 * Join date
 * Repositories starred,
 * Amount of public repositories
* A list of the popular repositories by the user, including the amount of stars and pulls for each repository
* A cached result of the widget using Transient, the cache timout can be adjusted via the widget settings

# Installation

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Add the widget to your desired location in Appearance->Widgets
4. Enter your Docker Hub username in the appropriate field


# Screenshots

![An example showing the Docker Hub Mini Profile Widget in use.](/screenshot-1.png?raw=true "Docker Hub Profile Widget")
![Adding the Docker Hub Profile Widget](/screenshot-2.png?raw=true "Adding the Docker Hub Profile Widget")


# Changelog

v1.2 - 2015-03-14
	* Made some code and name changes to match the Wordpress Plugin guidelines
	* Cache will now be cleared after deactivation of the plugin

v1.1 - 2015-03-05 
	* Changed plugin name to eawo-profile-widget-dockerhub
	* Added check on non-existent Docker Hub user

v1.0
	* Initial version

# Credits

This plugin is forked from f13dev's Github Profile Widget, which is available here:   https://wordpress.org/plugins/f13-github-mini-profile-widget/. 
I replaced the Github API calls with the Docker Hub API calls and changed the style to match the looks of Docker Hub.  So credits go to f13dev for his original plugin.

Credits go to the Github team for their “star.svg” and “cloud-download.svg” from: https://github.com/primer/octicons/tree/master/lib/svg
