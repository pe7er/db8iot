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
class PlgContentDb8iotArticleRead extends JPlugin
{
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart)
	{
		if ($context == 'com_content.article')
		{
			require("phpMQTT/phpMQTT.php");
			//thumbnail
			$MQTTBroker = $this->params->get('mqttbroker');
			$MQTTPort = $this->params->get('mqttport');
			$MQTTClient = $this->params->get('mqttclient');
			$channel = $this->params->get('channel'); // test
			$message  = $this->params->get('message'); // green_blink
			$QoS  = $this->params->get('qos'); // 0

			$MQTT = new phpMQTT($MQTTBroker, $MQTTPort, $MQTTClient);

			if ($MQTT->connect()) {
				$MQTT->publish("test", "Article `" . $article->title  . "` is being read at ".date("r"),0);
				$MQTT->publish($channel, $message, $QoS);

 				$MQTT->close();
			}
		}
		return;
	}
}