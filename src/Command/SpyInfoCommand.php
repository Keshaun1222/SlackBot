<?php
namespace SlackBot\Command;

use SlackBot\APICall;

class SpyInfoCommand extends Command{
    public function fire() {
        $client = $this->client;
        $db = $this->db;

        if (count($this->args) >= 1) {
            $nationID = $this->args[0];
            echo "Nation ID: " . $nationID . "\r\n";
            if (is_numeric($nationID)) {
                $nation = (new APICall())->call('nation', $nationID);
                if (!isset($nation['error'])) {
                    $where = array('target_id', 'int:' . $nationID);
                    $spies = $db->query('spy', $where);

                    $message = $nation['name'] . " Spy Information\r\n------------------------\r\n";
                    foreach ($spies as $spy) {
                        $date = new \DateTime($spy['timestamp'], new \DateTimeZone(getenv('TIMEZONE')));
                        $date->setTimezone(new \DateTimeZone('Africa/Dakar'));
                        $format = $date->format('(F d h:i A)');
                        $message .= $spy['message'] . " " . $format . "\r\n";
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
                    $message = "Nation does not exist. :pacman:\r\n";

                    $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                        $client->send($message, $channel);
                    });
                }
            } else {
                $message = "Invalid Arguments. Must use \"!spyinfo <nationid>\".";

                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                    $client->send($message, $channel);
                });
            }
        } else {
            $message = "Missing Arguments. Must use \"!spyinfo <nationid>\".";

            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
        }
    }
}