<?php
/**
 * Module for communicating with IoT
 *
 * @author     Peter Martin <joomla@db8.nl>
 * @copyright  Copyright 2016 Peter Martin
 * @license    GNU General Public License version 2 or later.
 * @link       https://db8.nl
 *
 * JavaScript code originally by Jan-Piet Mens:
 * http://jpmens.net/2014/07/03/the-mosquitto-mqtt-broker-gets-websockets-support/
 * https://github.com/jpmens/simple-mqtt-websocket-example
 *
 *
 * Mosquitto starts MQTT by default; Websockets is optional.
 * To switch ON websockets:
 * nano /etc/mosquitto/mosquitto.conf
 *
 * listener 9001
 * protocol websockets
 * listener 1883
 * protocol mqtt
 *
 * start mosquitto with that conf file:
 * mosquitto -c /etc/mosquitto/mosquitto.conf*
 */

defined('_JEXEC') or die;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_db8mqtt', $params->get('layout', 'default'));
