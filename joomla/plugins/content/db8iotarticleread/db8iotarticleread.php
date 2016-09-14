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
class PlgContentDb8iotarticleread extends JPlugin
{
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart)
	{
		if ($context == 'com_content.article')
		{
			$MQTTBroker = $this->params->get('mqttbroker');
			$MQTTPort = $this->params->get('mqttport');
			$MQTTClient = $this->params->get('mqttclient');

			require("phpMQTT/phpMQTT.php");

			$MQTT = new phpMQTT($MQTTBroker, $MQTTPort, $MQTTClient);

			if ($MQTT->connect()) {

				$channel = $this->params->get('channel'); // test

				$QoS  = $this->params->get('qos'); // 0

				$message =  "Article `" . $article->title  . " (ID: " . $article->id  . ")` is visited by by IP: ". $_SERVER['REMOTE_ADDR'] ." at ".date("r");
				$MQTT->publish($channel, $message , $QoS);

				$extramessage  = $this->params->get('extramessage'); // green_blink
				if ($extramessage)
				{
					$MQTT->publish($channel, $extramessage, $QoS);
				}

 				$MQTT->close();
			}
		}
		return;
	}
}