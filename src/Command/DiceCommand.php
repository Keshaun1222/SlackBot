<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;

class DiceCommand extends Command {
    public function fire() {
        $client = $this->client;

        if (count($this->args) == 0) {
            $sides = 6;
            $times = 1;
        } else {
            if(strstr($this->args[0], "d")) {
                $dice = explode("d", $this->args[0]);
                $sides = $dice[1];
                if ($dice[0] <= 10)
                    $times = $dice[0];
                else
                    $times = 10;
            } else if (count($this->args) >= 2) {
                $sides = $this->args[0];
                $times = $this->args[1];
            } else {
                $sides = $this->args[0];
                $times = 1;
            }
        }

        $message = "*Rolling...*\r\n";

        for ($i = 1; $i <= $times; $i++) {
            $message .= "Roll #" . $i . ": " . rand(1, $sides) . "\r\n";
        }

        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
            $client->send($message, $channel);
        });
    }
}