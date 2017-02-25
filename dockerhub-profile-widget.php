<?php
/*
Plugin Name: DockerHub Mini Profile Widget
Plugin URI: TBD
Description: Add a mini version of your DockerHub profile to a widget on a WordPress powered site.
Version: 1.0
Author: Eamon Woortman - eamonwoortman
Author URI: https://eamonwoortman.nl
Text Domain: dockerhub-mini-profile-widget
License: GPLv3
*/

/*
Copyright 2017 Eamon Woortman - eamonwoortman (contact@eamonwoortman.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

/**
* Register the widget
*/
add_action('widgets_init', create_function('', 'return register_widget("DockerHub_Mini_Profile_Widget");'));
// Register the css
add_action( 'wp_enqueue_scripts', 'ew_dhmpw_style');

/**
 * A function to register and enque the stylesheet
 */
function ew_dhmpw_style()
{
	wp_register_style( 'ew-dhmpw-style', plugins_url('dockerhub-profile-widget.css', __FILE__) );
	wp_enqueue_style( 'ew-dhmpw-style' );
}

/**
 * A class to generate a DockerHub profile widget
 */
class DockerHub_Mini_Profile_Widget extends WP_Widget
{
	/** Basic Widget Settings */
	const WIDGET_NAME = "DockerHub Mini Profile Widget";
	const WIDGET_DESCRIPTION = "Add a mini version of your DockerHub profile to your website.";

	var $textdomain;
	var $fields;

	/**
	* Create a new instance of the DockerHub widget
	* by setting the widget setting fields.
	*/
	function __construct()
	{
		$this->textdomain = strtolower(get_class($this));

		//Add fields
		$this->add_field('title', 'Widget title', '', 'text');
		$this->add_field('dockerhub_user', 'DockerHub ID', '', 'text');
		$this->add_field('dockerhub_timeout', 'Cache timeout (minutes)', '30', 'number');
		//Init the widget
		parent::__construct($this->textdomain, __(self::WIDGET_NAME, $this->textdomain), array( 'description' => __(self::WIDGET_DESCRIPTION, $this->textdomain), 'classname' => $this->textdomain));
	}

	/**
	* Widget frontend
	*
	* @param array $args
	* @param array $instance
	*/
	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);

		echo $args['before_widget'];

		if (!empty($title))
		echo $args['before_title'] . $title . $args['after_title'];

		$this->widget_output($args, $instance);

		echo $args['after_widget'];
	}

	/**
	* Adds a text field to the widget
	*
	* @param $field_name
	* @param string $field_description
	* @param string $field_default_value
	* @param string $field_type
	*/
	private function add_field($field_name, $field_description, $field_default_value, $field_type)
	{
		if(!is_array($this->fields))
		$this->fields = array();

		$this->fields[$field_name] = array('name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'type' => $field_type);
	}

	/**
	* Widget backend
	*
	* @param array $instance
	* @return string|void
	*/
	public function form( $instance )
	{
		/**
		* Create a header with basic instructions.
		*/
		?>
		<br/>
		Use this widget to add a mini version of your DockerHub profile as a widget<br/>
		<br/>
		Get your access token from <a href="https://github.com/settings/tokens" target="_blank">https://github.com/settings/tokens</a>.<br/>
		<br/>
		<?php
		/* Generate admin form fields */
		foreach($this->fields as $field_name => $field_data)
		{
			if($field_data['type'] === 'text')
			{
				?>
				<p>
					<label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" type="text" value="<?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?>" />
				</p>
				<?php

			}
			else
			if($field_data['type'] === 'number')
			{
				?>
				<p>
					<label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" type="number" value="<?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?>" />
				</p>
				<?php
			}
			else
			{
				/* Otherwise show an error */
				echo __('Error - Field type not supported', $this->textdomain) . ': ' . $field_data['type'];
			}
		}
	}

	/**
	* Updating widget by replacing the old instance with new
	*
	* @param array $new_instance
	* @param array $old_instance
	* @return array
	*/
	public function update($new_instance, $old_instance)
	{
		return $new_instance;
	}

	/**
	 * Function to load the widget
	 */
	private function widget_output($args, $instance)
	{
		extract($instance);

		// Set the cache name for this instance of the widget
		$cache = get_transient('wpdhpw' . md5(serialize($dockerhub_user)));

		if ($cache)
		{
				// If the cache exists, return it rather than re-creating it
				echo $cache;
		}
		else
		{
			// Get the API results
			$userAPI = $this->get_dockerhub_response('https://hub.docker.com/v2/users/' . $dockerhub_user . '/');
			$widget = '
				<div class="dhmpw-container">
                    <div class="dhmpw-head">
						<a href="https://hub.docker.com/u/' . $userAPI['username'] . '/" class="dhmpw-head-link">
							<div class="dhmpw-header">
                                <img src="'.plugins_url("img/docker-mini-logo.svg", __FILE__ ).'" alt="docker logo" class="dhmpw-docker-logo"/>
                                <div class="dhmpw-title">Docker Hub</div>
		                         <div class="dhmpw-profile-picture">
                                    <img src="' . $userAPI['gravatar_url'] . '">
                                </div>
                                <div class="dhmpw-user">
									@' . $userAPI['username'] . '
								</div>
							</div>							
						</a>
					</div>';
					
					
					$widget .= '
					<div class="dhmpw-info">
						<span class="dhmpw-info-user">
							<svg aria-hidden="true" height="16" version="1.1" viewBox="0 0 14 16" width="14"><path d="M4.75 4.95C5.3 5.59 6.09 6 7 6c.91 0 1.7-.41 2.25-1.05A1.993 1.993 0 0 0 13 4c0-1.11-.89-2-2-2-.41 0-.77.13-1.08.33A3.01 3.01 0 0 0 7 0C5.58 0 4.39 1 4.08 2.33 3.77 2.13 3.41 2 3 2c-1.11 0-2 .89-2 2a1.993 1.993 0 0 0 3.75.95zm5.2-1.52c.2-.38.59-.64 1.05-.64.66 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2-.65 0-1.17-.53-1.19-1.17.06-.19.11-.39.14-.59zM7 .98c1.11 0 2.02.91 2.02 2.02 0 1.11-.91 2.02-2.02 2.02-1.11 0-2.02-.91-2.02-2.02C4.98 1.89 5.89.98 7 .98zM3 5.2c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.45 0 .84.27 1.05.64.03.2.08.41.14.59C4.17 4.67 3.66 5.2 3 5.2zM13 6H1c-.55 0-1 .45-1 1v3c0 .55.45 1 1 1v2c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h1v3c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-3h1v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-2c.55 0 1-.45 1-1V7c0-.55-.45-1-1-1zM3 13H2v-3H1V7h2v6zm7-2H9V9H8v6H6V9H5v2H4V7h6v4zm3-1h-1v3h-1V7h2v3z"></path></svg>
                            ' . $userAPI['username'] . '<br />';

							if ($userAPI['location'] != '')
							{
								$widget .= '
								<svg aria-hidden="true" height="16" version="1.1" viewBox="0 0 12 16" width="12"><path d="M6 0C2.69 0 0 2.5 0 5.5 0 10.02 6 16 6 16s6-5.98 6-10.5C12 2.5 9.31 0 6 0zm0 14.55C4.14 12.52 1 8.44 1 5.5 1 3.02 3.25 1 6 1c1.34 0 2.61.48 3.56 1.36.92.86 1.44 1.97 1.44 3.14 0 2.94-3.14 7.02-5 9.05zM8 5.5c0 1.11-.89 2-2 2-1.11 0-2-.89-2-2 0-1.11.89-2 2-2 1.11 0 2 .89 2 2z"></path></svg>
								' . $userAPI['location'] . '<br />';
							}

						$widget .= '
						</span>
						<span class="dhmpw-info-website">';
							if ($userAPI['profile_url'] != '')
							{
								$widget .= '
								<svg aria-hidden="true" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path></svg>
								<a href="' . $userAPI['profile_url'] . '">' . $userAPI['profile_url'] . '</a><br />';
							}

							$widget .= '
							<svg aria-hidden="true" height="16" version="1.1" viewBox="0 0 14 16" width="14"><path d="M8 8h3v2H7c-.55 0-1-.45-1-1V4h2v4zM7 2.3c3.14 0 5.7 2.56 5.7 5.7s-2.56 5.7-5.7 5.7A5.71 5.71 0 0 1 1.3 8c0-3.14 2.56-5.7 5.7-5.7zM7 1C3.14 1 0 4.14 0 8s3.14 7 7 7 7-3.14 7-7-3.14-7-7-7z"></path></svg>
							Joined on ' . $this->gitDate($userAPI['date_joined']) . '
						</span>';
						
						$starredAPI = $this->get_dockerhub_response('https://hub.docker.com/v2/users/' . $dockerhub_user . '/repositories/starred/?page=1&page_size=1');
						$repositoriesAPI = $this->get_dockerhub_response('https://registry.hub.docker.com/v2/repositories/' . $dockerhub_user . '/?page=1&page_size=3');	
						$starredCount = $starredAPI['count'];
						$widget .= '
						<span>
							<svg width="14px" height="16px" viewBox="0 0 14 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Octicons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="star" fill="#000000"><polygon id="Shape" points="14 6 9.1 5.36 7 1 4.9 5.36 0 6 3.6 9.26 2.67 14 7 11.67 11.33 14 10.4 9.26"></polygon></g></g></svg>

							<a href="https://hub.docker.com/u/' . $userAPI['username'] . '/starred/">
								<span>
									<span>' . $starredCount . '</span> Starred
								</span>
							</a>
						</span>
						<span class="dhmpw-repos-public">
							<svg aria-hidden="true" class="octicon octicon-repo" height="16" version="1.1" viewBox="0 0 12 16" width="12"><path d="M4 9H3V8h1v1zm0-3H3v1h1V6zm0-2H3v1h1V4zm0-2H3v1h1V2zm8-1v12c0 .55-.45 1-1 1H6v2l-1.5-1.5L3 16v-2H1c-.55 0-1-.45-1-1V1c0-.55.45-1 1-1h10c.55 0 1 .45 1 1zm-1 10H1v2h2v-1h3v1h5v-2zm0-10H2v9h9V1z"></path></svg>
							<a href="https://hub.docker.com/u/' . $userAPI['username'] . '">' . $repositoriesAPI['count'] . ' Public Repos</a>
						</span>
					</div>';

					$widget .= '
					<div class="dhmpw-repos">
                        <span class="dhmpw-popular-repos">
                            <b>Popular repositories</b>
                        </span>
                    ';
					if($repositoriesAPI['count'] == 0) {
						$widget .= '
                        <span>This user has no repos yet</span>';
					}		

					foreach($repositoriesAPI['results'] as $repository) {
                        $repoUrl = "https://hub.docker.com/r/". $userAPI['username'] . "/" .$repository['name'] . "/";
                        $widget .= '
						<span class="dhmpw-repository">
							<span><a href="' . $repoUrl . '"> ' . $repository['name'] . '</a></span>
							<div class="dhmpw-repo-info">
								<img src="'.plugins_url("img/github-star-logo.svg", __FILE__ ).'"/> ' . $repository['star_count'] . ' 
								<img src="'.plugins_url("img/github-downloads-logo.svg", __FILE__ ).'"/> ' . $repository['pull_count'] . '
							</div>
						</span>
                        ';
					}

					$widget .= '
					</div>
				</div>
			</div>
			';
			$timeout = $dockerhub_timeout * 60;
			if ($timeout == 0)
			{
				$timeout = 1;
			}
			set_transient('wpdhpw' . md5(serialize($dockerhub_user)), $widget, $timeout);
			echo $widget;
		}
	}

	private function get_dockerhub_response($url)
	{
			// Start curl
			$curl = curl_init();
			// Set curl options
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPGET, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Accept: application/json'
			));

			// Set the user agent
			curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
			// Set curl to return the response, rather than print it
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			// Get the results
			$result = curl_exec($curl);

			// Close the curl session
			curl_close($curl);
			
			// Decode the results
			$result = json_decode($result, true);

			// Return the results
			return $result;
	}

	private function gitDate($date)
	{
		$dateArray = explode('-', $date);
		// Add the day to the string
		$date = substr($dateArray[2], 0, 2) . ' ';
		// Add the month to the string
		$date .= $this->getMonth($dateArray[1]) . ' ';
		// Add the year
		$date .= $dateArray[0];
		return $date;
	}

	private function getMonth($month)
	{
		if ($month == '01')
		{
			return 'Jan';
		}
		else
		if ($month == '02')
		{
			return 'Feb';
		}
		else
		if ($month == '03')
		{
			return 'Mar';
		}
		else
		if ($month == '04')
		{
			return 'Apr';
		}
		else
		if ($month == '05')
		{
			return 'May';
		}
		else
		if ($month == '06')
		{
			return 'Jun';
		}
		else
		if ($month == '07')
		{
			return 'Jul';
		}
		else
		if ($month == '08')
		{
			return 'Aug';
		}
		else
		if ($month == '09')
		{
			return 'Sep';
		}
		else
		if ($month == '10')
		{
			return 'Oct';
		}
		else
		if ($month == '11')
		{
			return 'Nov';
		}
		else
		if ($month == '12')
		{
			return 'Dec';
		}
	}

}
