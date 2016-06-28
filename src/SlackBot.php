<?php
namespace SlackBot;

use React\EventLoop\Factory;
use Slack\ConnectionException;
use Slack\RealTimeClient;
use SlackBot\Command\HelpCommand;
use SlackBot\Message\Message;

class SlackBot {
    public function __construct() {
        date_default_timezone_set(getenv('TIMEZONE'));
    }

    public function run() {
        $eventLoop = Factory::create();

        $client = new SlackRTMClient($eventLoop);
        $client->setToken(getenv('TOKEN'));

        $commandBindings = [
            'help' => HelpCommand::class,
        ];

        $manager = new Manager($client, $commandBindings);

        $client->on('message', function ($data) use ($client, $manager) {
            $message = new Message($data);

            if ($message->getSubType() == 'channel_join') {
                $client->refreshChannel($message->getChannel());
            } else if ($message->getSubType() == 'channel') {
                $client->refreshChannel($message->getChannel());
            } else {
                $manager->input($message);
            }
        });

        echo "Connecting...\r\n";
        $client->connect()->then(function() {
            echo "Connected.\n";
        }, function(ConnectionException $e) {
            echo $e->getMessage();
            exit();
        });

        $eventLoop->run();
    }
}