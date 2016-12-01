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
 * Class PlgUserDb8iotLoginCheck
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

		$errormsg = "ALARM! User " . $response['username'] . " from IP " . $_SERVER['REMOTE_ADDR']
			. " at " . date("Y-m-d H:i:s") ;


		$MQTTBroker = $this->params->get('mqttbroker');
		$MQTTPort = $this->params->get('mqttport');
		$MQTTClient = $this->params->get('mqttclient');

		require("phpMQTT/phpMQTT.php");

		$MQTT = new phpMQTT($MQTTBroker, $MQTTPort, $MQTTClient);

		if ($MQTT->connect()) {

			$channel = $this->params->get('channel'); // test

			$QoS  = $this->params->get('qos'); // 0

			$MQTT->publish($channel, $errormsg , $QoS);

			$extramessage  = $this->params->get('extramessage'); // red_on
			if ($extramessage)
			{
				$MQTT->publish($channel, $extramessage, $QoS);
			}

			$MQTT->close();
		}

	}
}