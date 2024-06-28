<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use PhpMqtt\Client\MQTTClient;

class OpenGateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $server   = env('MQTT_HOST');
        $port     = env('MQTT_PORT');
        $clientId = env('MQTT_CLIENTID');

        $mqtt = new MQTTClient($server, $port, $clientId);
        $mqtt->connect(env('MQTT_USERNAME'), env('MQTT_PASSWORD'));
        $mqtt->publish(env('MQTT_TOPIC'), 'open', 0);
        $mqtt->close();
    }
}
