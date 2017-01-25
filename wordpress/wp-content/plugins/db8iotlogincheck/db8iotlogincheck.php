<?php
/**
 * Plugin Name: db8 IoT Login Check
 * Plugin URI: https://github.com/pe7er/db8iot
 * Description: This plugin uses MQTT Communication to give a warning after a faulty login attempt
 * Version: 0.0.1
 * Author: Peter Martin
 * Author URI: http://db8.nl/
 * License: GNU General Public License version 2 or later
 *
 * Documentation used: https://codex.wordpress.org/Plugin_API/Filter_Reference/login_errors
 */

defined('ABSPATH') or die();

add_action( 'admin_menu', 'db8iotLoginCheck_menu' );

function db8iotLoginCheck_menu() {
	add_options_page( 'plugins.php', 'db8 IoT Login Check', 'manage_options', 'db8IoTLoginCheck', 'db8iotLoginCheck_options' );
}

function db8iotLoginCheck_options()
{
	if (!current_user_can('manage_options'))
	{
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	echo '<div class="wrap">';
	echo '<h1>db8 IoT Login Check</h1>';
	echo '<p>This plugin triggers MQTT Communication after a faulty login attempt.</p>';
	?>
	<p>
		<strong>Current settings (Hardcoded)</strong><br>
		MQTTBroker = 192.168.3.1<br>
		MQTTPort   = 1883<br>
		MQTTClient = WordPress website<br>
		QoS = 0<br>
		extra message = red_blink<br>
	</p>
<?php
	echo '</div>';
}

/**
 * @param $error
 *
 * @return string
 */
add_filter('login_errors', function( $error) {
	global $errors;
	$err_codes = $errors->get_error_codes();

	if (in_array('invalid_username', $err_codes, true))
	{
		// Send MQTT message $error
		sendMQTT('Wrong Username');
	}
	if ( in_array('incorrect_password', $err_codes, true) )
	{
		// Send MQTT message $error
		sendMQTT('Wrong Password');
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
	//$MQTTBroker = '192.168.3.1';
	$MQTTBroker = '192.168.3.1';
	$MQTTPort   = '1883';
	$MQTTClient = 'WordPress website';

	require_once __DIR__ . "/phpMQTT/phpMQTT.php";

	$MQTT = new phpMQTT($MQTTBroker, $MQTTPort, $MQTTClient);

	if ( $MQTT->connect() )
	{
		// Channel: Test
		$channel = 'test';
		// Default QoS = 0
		$QoS = 0;
		// Eg red_on
		$extramessage = 'red_blink';

		$MQTT->publish($channel, $error, $QoS);

		if ($extramessage)
		{
			$MQTT->publish($channel, $extramessage, $QoS);
		}

		$MQTT->close();
	}

	return;
}