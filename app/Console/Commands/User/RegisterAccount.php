<?php

namespace App\Console\Commands\User;

use App\Console\Elasticsearch\QueryDSL\Trip;
use App\Console\RabbitMQ\SetConnect;
use App\Facade\Elasticsearch;
use App\Jobs\RegisterAccount as JobsRegisterAccount;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RegisterAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi mail chúc mừng tạo tài khoản thành công';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Queue worker started');

        $amqpConfig = config('amqp.properties.production');

        $connection = new AMQPStreamConnection(
            $amqpConfig['host'],
            $amqpConfig['port'],
            $amqpConfig['username'],
            $amqpConfig['password'],
            $amqpConfig['vhost'],
            false,
            'AMQPLAIN',
            null,
            'en_US',
            30000,
            30000
        );

        $channel = $connection->channel();
        $exchange = 'exchange_yody';
        $queue = 'send-mail-register-account';
        $routingKey = 'send-mail-register-account';

        $channel->exchange_declare($exchange, 'topic', false, true, false, false, false);

        $channel->queue_declare($queue, false, true, false, false, false);

        $channel->queue_bind($queue, $exchange, $routingKey);

        $channel->basic_consume($queue, '', false, false, false, false, function ($msg) use ($exchange, $channel) {

            $this->info("started event_trip_elasticsearch");
            $strMessage = $msg->body;
            JobsRegisterAccount::dispatch($strMessage);

            //finish
        });

        // while (count($channel->callbacks)) {
        //     $channel->wait();
        // }
        $channel->close();
        $connection->close();
        $this->info('Queue worker stopped');
    }
}
