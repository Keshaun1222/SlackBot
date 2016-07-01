<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use SlackBot\APICall;

class AddSpyCommand extends Command {
    public function fire() {
        $client = $this->client;
        $db = $this->db;

        if (count($this->args) >= 2) {
            $nationID = 0;
            if (is_numeric($this->args[0])) {
                $nationID = array_shift($this->args);
                $spyMessage = implode(" ", $this->args);

                $nation = (new APICall())->call('nation', $nationID);
                if (!isset($nation['error'])) {
                    $cols = array('target_id', 'message');
                    $values = array('int' => $nationID, 'string' => $spyMessage);

                    $insert = $db->insert('spy', $cols, $values);
                    if ($insert) {
                        $message = "*Spy report logged!*\r\n";
                        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                            $client->send($message, $channel);
                        });
                    } else {
                        $message = "Could not log spy report.\r\n";
                        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                            $client->send($message, $channel);
                        });
                    }
                } else {
                    $message = "Nation does not exist. :pacman:\r\n";

                    $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                        $client->send($message, $channel);
                    });
                }
            } else {
                $spyMessage = implode(" ", $this->args);
                $first = explode("about ", $spyMessage);
                $nationName = explode(".", $first[1])[0];
                $nations = (new APICall())->call('nations');
                foreach ($nations['nations'] as $check) {
                    if ($check['nation'] == $nationName) {
                        $nationID = $check['nationid'];
                    }
                }
                if ($nationID != 0) {
                    $cols = array('target_id', 'message');
                    $values = array('int' => $nationID, 'string' => $spyMessage);

                    $insert = $db->insert('spy', $cols, $values);
                    if ($insert) {
                        $message = "*Spy report logged!*\r\n";
                        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                            $client->send($message, $channel);
                        });
                    } else {
                        $message = "Could not log spy report.\r\n";
                        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                            $client->send($message, $channel);
                        });
                    }
                } else {
                    $message = "Nation does not exist. :pacman:\r\n";

                    $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                        $client->send($message, $channel);
                    });
                }
            }
        } else {
            $message = "Missing Arguments. Must use \"!addspy [nationid] <message>\".";

            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
        }
    }
}