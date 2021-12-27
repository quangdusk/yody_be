<?php

namespace App\Console\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use App\Console\RabbitMQ\SetVariable;
use PhpAmqpLib\Message\AMQPMessage;

class SetConnect extends SetVariable
{
    public function __construct($queue, $routingKey)
    {
        $this->queue = $queue;
        $this->routingKey = $routingKey;
    }
    public function set_connect(\Closure $handler, $routingKeyNext)
    {
        $amqpConfig = config('amqp.properties.production');
        $connection = new AMQPStreamConnection($amqpConfig['host'], $amqpConfig['port'], $amqpConfig['username'], $amqpConfig['password'], $amqpConfig['vhost']);

        $channel = $connection->channel();
        $exchange = $this->exchange;
        $exchangeRetries = $this->exchangeRetries;
        $queue = $this->queue;
        $routingKey = $this->routingKey;
        $number_of_times = $this->number_of_times;

        $arguments =  new AMQPTable([
            'x-delayed-type' => 'topic'
        ]);

        $channel->exchange_declare($exchange, 'topic', false, true, false, false, false);
        $channel->exchange_declare($exchangeRetries, 'x-delayed-message', false, true, false, false, false, $arguments);
        $channel->queue_declare($queue, false, true, false, false, false);

        $channel->queue_bind($queue, $exchange, $routingKey);
        $channel->queue_bind($queue, $exchangeRetries, $routingKey);

        $channel->basic_qos(0, 1, false);
        
        $channel->basic_consume($queue, '', false, false, false, false, function ($msg) use ($handler, $channel, $exchange, $exchangeRetries, $routingKey, $routingKeyNext, $number_of_times) {
            $application_headers = $msg->get_properties();
            $xRetries = 1;
            if (isset($application_headers['application_headers'])) {
                $header = $application_headers['application_headers'];
                $nativeData = $header->getNativeData();

                if (count($nativeData) > 0) {
                    $xRetries = $nativeData['x-retries'] + 1;
                }
            }
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

            $strMessage = $msg->body;
            $json = $handler($strMessage);

            if ($json != false) {
                $channel->basic_publish(new AMQPMessage($json, ["application_headers" => []]), $exchange, $routingKeyNext);
            } else {
                if ($xRetries <= count($number_of_times)) {
                    dump("Số lần " . $xRetries . "-" . date("d-m-Y H:i:s"));
                    $appHeader = new AMQPTable([
                        "x-retries" => ($xRetries), // Số lần retry 
                        "x-delay" => $number_of_times[$xRetries]
                    ]);

                    $message = new AMQPMessage($strMessage, [
                        'content_type' => 'text/plain',
                        "delivery_mode" => 2,
                        "application_headers" => $appHeader
                    ]);

                    $channel->basic_publish($message, $exchangeRetries, $routingKey);
                }
            }
        });
        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
