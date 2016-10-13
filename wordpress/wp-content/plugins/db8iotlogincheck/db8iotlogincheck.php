<?php
/**
 * Plugin Name: db8 IoT Login Check
 * Plugin URI: https://github.com/pe7er/db8iot
 * Description: This plugin uses MQTT Communication to give a warning after a faulty login attempt
 * Version: 0.0.1
 * Author: Peter Martin
 * Author URI: http://db8.nl/
 * License: GNU General Public License version 2 or later
 * Documentation used: https://codex.wordpress.org/Plugin_API/Filter_Reference/login_errors
 */

defined('ABSPATH') or die();

add_filter(
	/**
 * @param $error
 *
 * @return string
 */
	'login_errors', function( $error) {
	global $errors;
	$err_codes = $errors->get_error_codes();

	if (in_array('invalid_username', $err_codes, true))
	{
		$error = '<strong>ERROR</strong>: Invalid username.';

		// Send MQTT message red_on
		sendMQTT('red_on');

		// Send MQTT message $error
		sendMQTT($error);
	}

	/* Incorrect password.
	   Default: '<strong>ERROR</strong>: The password you entered for the username <strong>%1$s</strong>
	   is incorrect. <a href="%2$s">Lost your password</a>?'
	*/
	if ( in_array('incorrect_password', $err_codes, true) )
	{
		$error = '<strong>ERROR</strong>: The password you entered is incorrect.';

		// Send MQTT message red_on
		sendMQTT('red_on');

		// Send MQTT message $error
		sendMQTT($error);
	}

	return $error;

	}
);

/**
 * Send MQTT message
 *
 * @param   $error  string  Error Message
 *
 * @return void
 */
function sendMQTT($error)
{
	$MQTTBroker = $this->params->get('mqttbroker');
	$MQTTPort   = $this->params->get('mqttport');
	$MQTTClient = $this->params->get('mqttclient');

	$MQTTBroker = '192.168.3.1';
	$MQTTPort   = $this->params->get('mqttport');
	$MQTTClient = $this->params->get('mqttclient');

	require __DIR__ . "phpMQTT/phpMQTT.php";

	$MQTT = new phpMQTT($MQTTBroker, $MQTTPort, $MQTTClient);

	if ( $MQTT->connect() )
	{
		// Channel: Test
		$channel = $this->params->get('channel');

		// Default QoS = 0
		$QoS = $this->params->get('qos');

		$MQTT->publish($channel, $error, $QoS);

		// Eg red_on
		$extramessage = $this->params->get('extramessage');

		if ($extramessage)
		{
			$MQTT->publish($channel, $extramessage, $QoS);
		}

		$MQTT->close();
	}

	return;
}