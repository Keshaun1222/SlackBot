<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use SlackBot\APICall;

class NationCommand extends Command {
    public function fire() {
        $client = $this->client;
        $db = $this->db;
        $user = $this->userId;
        
        $where = array('user_id' => 'string:' . $user);

        if (count($this->args) >= 1 || $db->query('link', $where)) {
            $nationID = (count($this->args) >= 1 && is_numeric($this->args[0]) ? $this->args[0] : ($db->query('link', $where) ? $db->query('link', $where)[0]['nation_id'] : "Na"));
            if (is_numeric($nationID)) {
                $nation = (new APICall())->call("nation", $nationID);

                if (!isset($nation['error'])) {
                    $message = "Nation Information\r\n------------------------\r\n";
                    $message .= "Nation Name: " . $nation['name'] . " || ";
                    $message .= "Nation Link: https://politicsandwar.com/nation/id=" . $nation['nationid'] . " || ";
                    $message .= "Score: " . $nation['score'] . " || ";
                    $message .= "Nation Rank: " . $nation['nationrankstrings'] . "\r\n";
                    $message .= "# of Cities: " . count($nation['cityids']) . " || ";
                    $message .= "Total Infrastructure: " . number_format($nation['totalinfrastructure']) . " || ";
                    $message .= "Total Land Area: " . number_format($nation['landarea']) . " || ";
                    $message .= "Total Population: " . number_format($nation['population']) . "\r\n";
                    $message .= "Soldiers: " . number_format($nation['soldiers']) . " || ";
                    $message .= "Tanks: " . number_format($nation['tanks']) . " || ";
                    $message .= "Aircrafts: " . number_format($nation['aircraft']) . " || ";
                    $message .= "Missles: " . number_format($nation['missiles']) . " || ";
                    $message .= "Nukes: " . number_format($nation['nukes']) . "\r\n";

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
