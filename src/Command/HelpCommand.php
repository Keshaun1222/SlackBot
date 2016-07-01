<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use Slack\DirectMessageChannel;

class HelpCommand extends Command {
    public function fire() {
        $client = $this->client;

        $message = "Available Commands\r\n------------------------\r\n";
        $message .= "!nation <nationid> - Lookup the stats of a nation.\r\n";
        $message .= "!raiding <nationid> [none|beige|vacation|both] - Lookup raiding targets with your nation's war range.\r\n";
        $message .= "!wars <nationid> - Gets the active wars for a nation.\r\n";
        $message .= "!spyinfo <nationid> - Gets the recorded spy information for a nation\r\n";
        $message .= "!addspy [nationid] <message> - Adds a spy report for a nation.\r\n";

        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
            $client->send($message, $channel);
        });

        /*$this->client->getDMByUserId($this->userId)->then(function (DirectMessageChannel $dm) use ($client, $message) {
            $client->send($message, $dm);
        });

        if ($this->channel[0] != 'D') {
            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client) {
                $client->send(":book: Please check your Direct Messages for help text.", $channel);
            });
        }*/
    }
}