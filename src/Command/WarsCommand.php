<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use SlackBot\APICall;

class WarsCommand extends Command {
    public function fire() {
        $client = $this->client;
        $db = $this->db;
        $user = $this->userId;

        $where = array('user_id' => 'string:' . $user);

        if (count($this->args) >= 1 || $db->query('link', $where)) {
            $nationID = (count($this->args) >= 1 && is_numeric($this->args[0]) ? $this->args[0] : ($db->query('link', $where) ? $db->query('link', $where)[0]['nation_id'] : "Na"));
            if (is_numeric($nationID)) {
                $nation = (new APICall())->call("nation", $nationID);

                $param = (count($this->args) >= 2 && is_numeric($this->args[0]) ? $this->args[1] : (count($this->args) >= 1 ? $this->args[0] : "none"));

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

                    if (strtolower($param) == 'offensive' || strtolower($param) == 'offense' || strtolower($param) == 'off' || strtolower($param) == 'agg' || strtolower($param) == 'atk') {
                        $message = "*" . $nation['name'] . "'s Offensive Wars*\r\n";
                        foreach ($offensives as $offense) {
                            $other = (new APICall())->call("nation", $offense['def_id']);
                            $name = $other['leadername'];
                            $message .= "vs. " . $name . " of " . $offense['def_name'] . " - Timeline: https://politicsandwar.com/nation/war/timeline/war=" . $offense['timeline_id'] . "\r\n";
                        }
                    } else if (strtolower($param) == 'defensive' || strtolower($param) == 'defense' || strtolower($param)== 'def') {
                        $message = "*" . $nation['name'] . "'s Defensive Wars*\r\n";
                        foreach ($defensives as $defense) {
                            $other = (new APICall())->call("nation", $defense['atk_id']);
                            $name = $other['leadername'];
                            $message .= "vs. " . $name . " of " . $defense['atk_name'] . " - Timeline: https://politicsandwar.com/nation/war/timeline/war=" . $defense['timeline_id'] . "\r\n";
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
            } else if ($nationID == "na") {
                $message = "No nation is linked to your slack account. Please use the *!link* command, or provide a nation id for *!raiding*.";

                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                    $client->send($message, $channel);
                });
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

    public function help() {
        return "!wars [nationid] [offensive|offense|off|agg|atk|defensive|defense|def] - Gets the active wars for a nation.";
    }
}