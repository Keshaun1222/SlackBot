<?php
namespace SlackBot;

use React\EventLoop\Factory;
use Slack\ConnectionException;
use Slack\RealTimeClient;
use SlackBot\Command\DiceCommand;
use SlackBot\Command\ETACommand;
use SlackBot\Command\HelpCommand;
use SlackBot\Command\NationCommand;
use SlackBot\Command\RaidingCommand;
use SlackBot\Command\WarsCommand;
use SlackBot\Command\SpyInfoCommand;
use SlackBot\Command\AddSpyCommand;
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
            'nation' => NationCommand::class,
            'raiding' => RaidingCommand::class,
            'wars' => WarsCommand::class,
            'spyinfo' => SpyInfoCommand::class,
            'addspy' => AddSpyCommand::class,
            'jello' => ETACommand::class,
            'eta' => ETACommand::class,
            'dice' => DiceCommand::class
        ];

        $db = new DB();

        $manager = new Manager($client, $commandBindings, $db);

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