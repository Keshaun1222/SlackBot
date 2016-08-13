<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use SlackBot\APICall;

class LinkCommand extends Command {
    public function fire() {
        $client = $this->client;
        $user = $this->userId;
        $db = $this->db;

        if (count($this->args) >= 1) {
            if (is_numeric($this->args[0])) {
                $nationID = $this->args[0];
                $nation = (new APICall())->call('nation', $nationID);
                if (!isset($nation['error'])) {
                    $where = array('nation_id' => 'int:' . $nationID);
                    if (!$db->query('link', $where)) {
                        $check = array('user_id' => 'string:' . $user);
                        if (!$db->query('link', $check)) {
                            $cols = array('nation_id', 'user_id');
                            $values = array('int' => $nationID, 'string' => $user);

                            $insert = $db->insert('link', $cols, $values);
                            if ($insert) {
                                $message = $nation['name'] ." is now linked to you!\r\n";
                                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                                    $client->send($message, $channel);
                                });
                            }
                        } else {
                            $values = array('nation_id', 'int:' . $nationID);
                            $userWhere = array('user_id', 'string:' . $user);

                            $update = $db->update('link', $values, $userWhere);
                            if ($update) {
                                $message = $nation['name'] ." is now linked to you! :smile:\r\n";
                                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                                    $client->send($message, $channel);
                                });
                            }
                        }
                    } else if ($db->query('link', $where)[0]['user_id'] == $user) {
                        $message = $nation['name'] ." is already linked to you! :laughing:\r\n";
                        $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                            $client->send($message, $channel);
                        });
                    } else {
                        $message = $nation['name'] ." is already linked to another user! :frowning:\r\n";
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
                $message = "Invalid Arguments. Must use \"!link <nationid>\".";

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

    public function help() {
        return "!link <nationid> - Links your nation to your slack account. This allows you to use other commands for your nation without specifying your nation id.";
    }
}