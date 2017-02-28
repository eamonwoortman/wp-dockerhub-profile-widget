# wp-dockerhub-profile-widget
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

1.0
* Initial release

# Credits

Credits goes to James Valentine for creating the original Github Profile Widget. I merely changed the API calls and visuals of his plugin so go to his repository and star him: https://github.com/f13dev/wp-github-profile-widget.

Credits go to the Github team for their “star.svg” and “cloud-download.svg” from: https://github.com/primer/octicons/tree/master/lib/svg