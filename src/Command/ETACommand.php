<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;

class ETACommand extends Command {
    public function fire() {
        $client = $this->client;

        $message = "ETA: Soon:tm: :pacman:\r\n";

        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
            $client->send($message, $channel);
        });
    }
}