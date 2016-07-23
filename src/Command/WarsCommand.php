<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use SlackBot\APICall;

class WarsCommand extends Command {
    public function fire() {
        $client = $this->client;

        if (count($this->args) >= 1) {
            $nationID = $this->args[0];
            if (is_numeric($nationID)) {
                $nation = (new APICall())->call("nation", $nationID);
                if (!isset($nation['error'])) {
                    $offensives = [];
                    $defensives = [];

                    $wars = (new APICall())->call("war");
                    foreach ($wars as $war) {
                        if ($war['atk_id'] == $nation['nationid']) {
                            $offensives[] = $war;
                        } else if ($war['def_id'] == $nation['nationid']) {
                            $defensives[] = $war;
                        }
                    }

                    if (count($this->args) >= 2 && ($this->args[1] == 'offensive' || $this->args[1] == 'offense')) {
                        $message = "*" . $nation['name'] . "'s Offensive Wars*\r\n";
                        foreach ($offensives as $offense) {
                            $other = (new APICall())->call("nation", $offense['def_id']);
                            $name = $other['leadername'];
                            $message .= "vs. " . $name . " of " . $offense['def_name'] . " - Timeline: https://politicsandwar.com/nation/war/timeline/war=" . $offense['timeline_id'] . "\r\n";
                        }
                    } else if (count($this->args >= 2) && ($this->args[1] == 'defensive' || $this->args[1] == 'defense')) {
                        $message = "*" . $nation['name'] . "'s Defensive Wars*\r\n";
                        foreach ($defensives as $defense) {
                            $other = (new APICall())->call("nation", $defense['def_id']);
                            $name = $other['leadername'];
                            $message .= "vs. " . $name . " of " . $defense['def_name'] . " - Timeline: https://politicsandwar.com/nation/war/timeline/war=" . $defense['timeline_id'] . "\r\n";
                        }
                    } else {
                        $message = "*" . $nation['name'] . "'s Wars*\r\n";
                        $message .= "Offensive Wars\r\n------------------------\r\n";
                        foreach ($offensives as $offense) {
                            $other = (new APICall())->call("nation", $offense['def_id']);
                            $name = $other['leadername'];
                            $message .= "vs. " . $name . " of " . $offense['def_name'] . " - Timeline: https://politicsandwar.com/nation/war/timeline/war=" . $offense['timeline_id'] . "\r\n";
                        }

                        $message .= "Defensive Wars\r\n------------------------\r\n";
                        foreach ($defensives as $defense) {
                            $other = (new APICall())->call("nation", $defense['def_id']);
                            $name = $other['leadername'];
                            $message .= "vs. " . $name . " of " . $defense['def_name'] . " - Timeline: https://politicsandwar.com/nation/war/timeline/war=" . $defense['timeline_id'] . "\r\n";
                        }
                    }

                    $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                        $client->send($message, $channel);
                    });
                } else {
                    $message = "Nation does not exist. :pacman:\r\n";

                    $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                        $client->send($message, $channel);
                    });
                }
            } else {
                $message = "Invalid Arguments. Must use \"!wars <nationid>\".";

                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                    $client->send($message, $channel);
                });
            }
        } else {
            $message = "Missing Arguments. Must use \"!wars <nationid>\".";

            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
        }
    }
}