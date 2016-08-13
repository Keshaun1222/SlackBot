<?php
namespace SlackBot\Command;


use Slack\ChannelInterface;
use SlackBot\APICall;

class RangeCommand extends Command {
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
                    $war = array (0.75 * $nation['score'], 1.75 * $nation['score']);
                    $spy = array(0.6 * $nation['score'], 1.67 * $nation['score']);
                    $message = "*" . $nation['name'] . "*\r\n";
                    if ($param != 'spy')
                        $message .= "War Range: " . $war[0] . " - " . $war[1] . "\r\n";
                    if ($param != 'war')
                        $message .= "Spy Range: " . $spy[0] . " - " . $spy[1] . "\r\n";

                    $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                        $client->send($message, $channel);
                    });
                }
            }
        }
    }
}