<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use Slack\DirectMessageChannel;

class HelpCommand extends Command {
    public function fire() {
        $client = $this->client;

        $message = 'Available Commands\r\n------------------\r\n';
        $message .= 'None yet :(\r\n';

        $this->client->getDMById($this->userId)->then(function (DirectMessageChannel $dm) use ($client, $message) {
            $client->send($message, $dm);
        });

        if ($this->channel[0] != 'D') {
            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client) {
                $client->send(':book: Please check your Direct Messages for help text.', $channel);
            });
        }
    }
}