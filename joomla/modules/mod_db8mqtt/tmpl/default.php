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
 */

defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->addScript( JUri::base() . '/modules/mod_db8mqtt/assets/mqttws31.js');

$MQTTBroker = $params->get('mqttbroker'); //'192.168.0.73';
$MQTTPort = $params->get('mqttport'); //9001;
$MQTTTopic = $params->get('mqtttopic'); // test, '#'
?>

<script type="text/javascript">
	var mqtt;
	var reconnectTimeout = 2000;

	host = '<?php echo $MQTTBroker; ?>';
	port = <?php echo $MQTTPort; ?>;
	topic = '<?php echo $MQTTTopic; ?>';
	useTLS = false;
	username = null;
	password = null;
	// path = "/mqtt";
	cleansession = true;

	function MQTTconnect() {
		if (typeof path == "undefined") {
			//path = '/mqtt';
			path ='';
		}
		mqtt = new Paho.MQTT.Client(
			host,
			port,
			path,
			"web_" + parseInt(Math.random() * 100, 10)
		);
		var options = {
			timeout: 3,
			useSSL: useTLS,
			cleanSession: cleansession,
			onSuccess: onConnect,
			onFailure: function (message) {
				jQuery('#status').val("Connection failed: " + message.errorMessage + "Retrying");
				setTimeout(MQTTconnect, reconnectTimeout);
			}
		};

		mqtt.onConnectionLost = onConnectionLost;
		mqtt.onMessageArrived = onMessageArrived;

		if (username != null) {
			options.userName = username;
			options.password = password;
		}
		console.log("Host="+ host + ", port=" + port + ", path=" + path + " TLS = " + useTLS + " username=" + username + " password=" + password);
		mqtt.connect(options);
	}

	function onConnect() {
		jQuery('#status').val('Connected to ' + host + ':' + port + path);
		// Connection succeeded; subscribe to our topic
		mqtt.subscribe(topic, {qos: 0});
		jQuery('#topic').val(topic);
	}

	function onConnectionLost(response) {
		setTimeout(MQTTconnect, reconnectTimeout);
		jQuery('#status').val("connection lost: " + responseObject.errorMessage + ". Reconnecting");

	};

	function onMessageArrived(message) {

		var topic = message.destinationName;
		var payload = message.payloadString;
		//	jQuery('#ws').prepend('<li>' + topic + ' = ' + payload + '</li>');
		jQuery('#ws').prepend('<li>' + payload + '</li>');
	};

	jQuery(document).ready(function() {
		MQTTconnect();
	});

</script>

<div>
	<?php /*
	<div>Subscribed to <input type='text' id='topic' disabled />
		Status: <input type='text' id='status' size="80" disabled /></div>
	*/ ?>
	<ul id='ws' style="font-family: 'Courier New', Courier, monospace;"></ul>
</div>
