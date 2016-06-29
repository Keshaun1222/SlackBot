<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use SlackBot\APICall;

class NationCommand extends Command {
    public function fire() {
        $client = $this->client;

        if (count($this->args) >= 1) {
            $nationID = $this->args[0];
            if (is_numeric($nationID)) {
                $nation = (new APICall())->call("nation", $nationID);

                $message = "Nation Information\r\n------------------------\r\n";
                $message .= "Nation Name: " . $nation['name'] . " || ";
                $message .= "Nation Link: https://politicsandwar.com/nation/id=" . $nation['nationid'] . " || ";
                $message .= "Nation Rank: " . $nation['nationrankstrings'] . "\r\n";
                $message .= "# of Cities: " . count($nation['cityids']) . " || ";
                $message .= "Total Infrastructure: " . $nation['totalinfrastructure'] . " || ";
                $message .= "Total Land Area: " . $nation['landarea'] . "\r\n";

                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                    $client->send($message, $channel);
                });
            } else {
                $message = "Invalid Arguments. Must use \"!nation <nationid>\".";

                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                    $client->send($message, $channel);
                });
            }
        } else {
            $message = "Missing Arguments. Must use \"!nation <nationid>\".";

            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
        }
    }
}