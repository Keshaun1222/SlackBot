<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;

class QuitCommand extends Command {
    public function fire() {
        $client = $this->client;
        //$loop = $client->getLoop();

        if ($this->userId == "U1DRN6DJQ") {
            $message = "CrimsonBot is now *OFFLINE*!\r\n";
            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
            $client->disconnect();
        } else {
            $message = "You are not authorized to use that command!\r\n";
            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
        }
    }
}