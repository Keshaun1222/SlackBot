<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use Slack\DirectMessageChannel;
use SlackBot\APICall;

class RaidingCommand extends Command {
    public function fire() {
        $client = $this->client;

        if (count($this->args) >= 1) {
            $nationID = $this->args[0];
            if (is_numeric($nationID)) {
                $nation = (new APICall())->call("nation", $nationID);

                $max = 1.75 * $nation['score'];
                $min = 0.75 * $nation['score'];
                echo "Min: " . $min . "; Max: " . $max . "\r\n";

                //$nations = [];
                $nationsList = (new APICall())->call("nations");
                echo "Count: " . count($nationsList) . "\r\n";

                $message = "Raiding Targets\r\n------------------------\r\n";

                foreach ($nationsList['nations'] as $target) {
                    if ($target['allianceid'] == 0 && $target['color'] != 'beige') {
                        if ($target['score'] >= $min && $target['score'] <= $max) {
                            $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . "\r\n";
                        }
                    }
                }

                $this->client->getDMByUserId($this->userId)->then(function (DirectMessageChannel $dm) use ($client, $message) {
                    $client->send($message, $dm);
                });

                if ($this->channel[0] != 'D') {
                    $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client) {
                        $client->send("Please check your Direct Messages for list of raiding targets.", $channel);
                    });
                }
            } else {
                $message = "Invalid Arguments. Must use \"!raiding <nationid>\".";

                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                    $client->send($message, $channel);
                });
            }
        } else {
            $message = "Missing Arguments. Must use \"!raiding <nationid>\".";

            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
        }

    }
}