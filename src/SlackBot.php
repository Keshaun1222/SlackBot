<?php
namespace SlackBot;

use React\EventLoop\Factory;
use Slack\ChannelInterface;
use Slack\ConnectionException;
use Slack\RealTimeClient;
use SlackBot\Command\CoinCommand;
use SlackBot\Command\DiceCommand;
use SlackBot\Command\ETACommand;
use SlackBot\Command\HelpCommand;
use SlackBot\Command\LinkCommand;
use SlackBot\Command\NationCommand;
use SlackBot\Command\QuitCommand;
use SlackBot\Command\RaidingCommand;
use SlackBot\Command\RangeCommand;
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
            'range' => RangeCommand::class,
            'dice' => DiceCommand::class,
            'coin' => CoinCommand::class,
            'link' => LinkCommand::class,
            'jello' => ETACommand::class,
            'eta' => ETACommand::class,
            'quit' => QuitCommand::class,
            'exit' => QuitCommand::class,
            'bye' => QuitCommand::class
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
        $client->connect()->then(function() use ($client) {
            echo "Connected.\n";
            $client->getChannelById("C1AMVKBQD")->then(function (ChannelInterface $testChan) use ($client) {
                $client->send("CrimsonBot is now *ONLINE*!\r\n", $testChan);
            });
        }, function(ConnectionException $e) {
            echo $e->getMessage();
            exit();
        });

        $eventLoop->run();
    }
}