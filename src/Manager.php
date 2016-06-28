<?php
namespace SlackBot;

use Exception;
use Slack\Channel;
use Slack\ChannelInterface;
use Slack\DirectMessageChannel;
use Slack\RealTimeClient;
use SlackBot\Command\Command;
use SlackBot\Message\Message;

class Manager {
    private $commandBindings;
    private $client;

    public function __construct(RealTimeClient $client, array $commandBindings) {
        $this->commandBindings = $commandBindings;
        $this->client = $client;
    }

    public function input(Message $message) {
        $input = $message->getText();

        if (!is_string($input)) {
            return false;
        }

        if (!isset($input[0])) {
            return false;
        }

        if ($input[0] !== '!') {
            return false;
        }

        $input_array = explode(' ', $input);
        $command = $input_array[0];

        if (strlen($command) < 2) {
            return false;
        }

        $command = substr($command, 1);
        $args = [];

        foreach($input_array as $i => $arg) {
            if ($i == 0) continue;

            if (empty($arg)) continue;

            $args[] = $arg;
        }

        if ($command == null) {
            return false;
        }

        $command = strtolower($command);
        if (!isset($this->commandBindings[$command])) {
            return false;
        }

        try {
            $command = new $this->commandBindings[$command]($this->client, $this, $message, $args);
            $command->fire();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}