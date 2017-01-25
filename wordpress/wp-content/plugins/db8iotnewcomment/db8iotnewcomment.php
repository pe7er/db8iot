<?php
/**
 * Plugin Name: db8 IoT New Comment
 * Plugin URI: https://github.com/pe7er/db8iot
 * Description: This plugin triggers MQTT Communication when a new comment is posted
 * Version: 0.0.1
 * Author: Peter Martin
 * Author URI: http://db8.nl/
 * License: GNU General Public License version 2 or later
 *
 * Documentation used: https://developer.wordpress.org/reference/hooks/comment_post/
 */


defined( 'ABSPATH' ) or die( );

add_action( 'admin_menu', 'db8iotNewComment_menu' );

function db8iotNewComment_menu() {
	add_options_page( 'plugins.php', 'db8 IoT Login Check', 'manage_options', 'db8IoTLoginCheck', 'db8iotNewComment_options' );
}

function db8iotNewComment_options()
{
	if (!current_user_can('manage_options'))
	{
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	echo '<div class="wrap">';
	echo '<h1>db8 IoT New Comment</h1>';
	echo '<p>This plugin triggers MQTT Communication after a new comment has been added.</p>';
	?>
	<p>
		<strong>Current settings (Hardcoded)</strong><br>
		MQTTBroker = 192.168.3.1<br>
		MQTTPort   = 1883<br>
		MQTTClient = WordPress website<br>
		QoS = 0<br>
		extra message = green_blink<br>
	</p>
	<?php
	echo '</div>';
}

function show_message_function( $comment_ID, $comment_approved ) {
	if( 1 === $comment_approved ){
		//function logic goes here

		// Send MQTT message $error
		echo "MQTT";
		sendGreenMQTT('Comment approved');

	}else{
		echo "MQTT not approved";
		sendGreenMQTT('New Comment');
	}
}

add_action( 'comment_post', 'show_message_function', 10, 0 );

/**
 * Send MQTT message
 *
 * @param   $error  string  Error Message
 *
 * @return void
 */
function sendGreenMQTT($error)
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
		$extramessage = 'green_blink';

		$MQTT->publish($channel, $error, $QoS);

		if ($extramessage)
		{
			$MQTT->publish($channel, $extramessage, $QoS);
		}

		$MQTT->close();
	}

	return;
}