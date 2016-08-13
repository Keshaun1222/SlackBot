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
                if ($dice[1] <= PHP_INT_MAX)
                    $sides = $dice[1];
                else
                    $sides = PHP_INT_SIZE;
                if ($dice[0] <= 10)
                    $times = $dice[0];
                else
                    $times = 10;
            } else if (count($this->args) >= 2) {
                if ($this->args[0] <= PHP_INT_MAX)
                    $sides = $this->args[0];
                else
                    $sides = PHP_INT_SIZE;
                if ($this->args[1] <= 10)
                    $times = $this->args[1];
                else
                    $times = 10;
            } else {
                if ($this->args[0] <= PHP_INT_MAX)
                    $sides = $this->args[0];
                else
                    $sides = PHP_INT_SIZE;
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

    public function help() {
        return "!dice [s|nds] [n] - Rolls up to 10 dices. See <http://www.knightsradiant.pw/topic/2155-crimsonbot/#comment-36549|this forum post> for clarification.";
    }
}