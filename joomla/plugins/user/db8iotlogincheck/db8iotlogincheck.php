<?php
/**
 * User Plugin for communicating with IoT
 *
 * @author     Peter Martin <joomla@db8.nl>
 * @copyright  Copyright 2016 Peter Martin
 * @license    GNU General Public License version 2 or later.
 * @link       https://db8.nl
 */

defined('_JEXEC') or die;

/**
 * Class PlgUserDb8iot
 *
 * @since  May 2016
 */
class PlgUserDb8iotLoginCheck extends JPlugin
{
	public function onUserLoginFailure($response)
	{
		$errorlog = array();

		switch ($response['status'])
		{
			case JAuthentication::STATUS_SUCCESS:
				$errorlog['status']  = $response['type'] . " CANCELED: ";
				$errorlog['comment'] = $response['error_message'];
				break;

			case JAuthentication::STATUS_FAILURE:
				$errorlog['status']  = $response['type'] . " FAILURE: ";

				if ($this->params->get('log_username', 0))
				{
					$errorlog['comment'] = $response['error_message'] . ' ("' . $response['username'] . '")';
				}
				else
				{
					$errorlog['comment'] = $response['error_message'];
				}
				break;

			default:
				$errorlog['status']  = $response['type'] . " UNKNOWN ERROR: ";
				$errorlog['comment'] = $response['error_message'];
				break;
		}

		$errormsg = "At " . JURI::root() . " user " . $response['username'] . " from IP " . $_SERVER['REMOTE_ADDR']
			. " and got ". $errorlog['status'] . $errorlog['comment'] . " at " . date("Y-m-d H:i:s") ;

		require("phpMQTT/phpMQTT.php");

		?>
		<pre><?php
		echo __FILE__ . '::' . __LINE__ . ':: ';
		echo ': ';
		echo '<div style="font-size: 1.5em;">';
		print_r($errormsg);
		echo '</div>';
		?></pre><?php
die("stop");

		$mqtt = new phpMQTT("192.168.0.24", 1883, "phpMQTT Pub Example"); //Change client name to something unique

		if ($mqtt->connect()) {
			$mqtt->publish("test", $errormsg . " and Hello JanBeyond at ".date("r"),0);
			$mqtt->publish("test", 'red_on',0);

			//$mqtt->publish("test","Hello World! at ".date("r"),0);
			$mqtt->close();
		}
	}
}