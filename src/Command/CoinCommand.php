<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;

class CoinCommand extends Command {
    public function fire() {
        $client = $this->client;

        $rand = rand(1, 4);
        $coin = ($rand > 2 ? "Heads" : "Tails");

        $message = "*Flipping the coin...*\r\n";
        $message .= "Coin landed on *" . $coin . "*.";

        if (count($this->args) > 0) {
            if (strtolower($coin) == strtolower($this->args[0]) || substr(strtolower($coin), 0, 1) == strtolower($this->args[0])) {
                $message .= " You guessed correctly! :smile:";
            } else {
                $message .= " You guessed incorrectly! :frowning:";
            }
        }

        $message .= "\r\n";

        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
            $client->send($message, $channel);
        });
    }
}