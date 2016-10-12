<?php
/**
 * Plugin Name: db8 IoT Login Check
 * Plugin URI: https://github.com/pe7er/db8iot
 * Description: This plugin triggers MQTT Communication after a faulty login attempt
 * Version: 0.0.1
 * Author: Peter Martin
 * Author URI: http://db8.nl/
 * License: GNU General Public License version 2 or later
 *
 * db8 IoT Login Check is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * db8 IoT Login Check is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with {Plugin Name}. If not, see {License URI}.
 */

defined('ABSPATH') or die();


/* my first attempt */

//$errors = new WP_Error();
// ...
//login_header( __( 'Reset Password', 'textdomain' ), '<p class="message reset-pass">' . __( 'Enter your new password below.', 'textdomain' ) . '</p>', $errors );

// https://developer.wordpress.org/reference/hooks/login_errors/
//$myError = apply_filters( 'login_errors', $errors);


function custom_login() {
	$creds = array();
	//$creds['user_login'] = 'example';
	//$creds['user_password'] = 'plaintextpw';
	$creds['remember'] = false;
	$user              = wp_signon( $creds, false );
	if ( is_wp_error( $user ) ) {
		echo "MQTT here" . $user->get_error_message();
	}
}

// run it before the headers and cookies are sent
add_action( 'after_setup_theme', 'custom_login' );

/*

print_r($myError);

print"<pre>";
print_r($_POST);
print"</pre>";

die("stop!");

	if ($myError){
		die ('myError');
	}
*/

/**
 * MQTT
 *
 * @return
 */
function MQTT()
{
	$MQTTBroker = $this->params->get( 'mqttbroker' );
	$MQTTPort   = $this->params->get( 'mqttport' );
	$MQTTClient = $this->params->get( 'mqttclient' );

	require( "phpMQTT/phpMQTT.php" );

	$MQTT = new phpMQTT( $MQTTBroker, $MQTTPort, $MQTTClient );

	if ( $MQTT->connect() )
	{
		// Test
		$channel = $this->params->get('channel');

		// Default  QoS = 0
		$QoS = $this->params->get('qos');

		$MQTT->publish($channel, $errormsg, $QoS);

		$extramessage = $this->params->get( 'extramessage' ); // red_on
		if ( $extramessage ) {
			$MQTT->publish( $channel, $extramessage, $QoS );
		}

		$MQTT->close();
	}

}