<?php
namespace SlackBot\Command;

use Slack\ChannelInterface;
use Slack\DirectMessageChannel;
use SlackBot\APICall;

class RaidingCommand extends Command {
    public function fire() {
        $client = $this->client;

        if (count($this->args) >= 1) {
            $nationID = $this->args[0];
            if (is_numeric($nationID)) {
                $nation = (new APICall())->call("nation", $nationID);

                if (!isset($nation['error'])) {
                    $max = 1.75 * $nation['score'];
                    $min = 0.75 * $nation['score'];
                    echo "Min: " . $min . "; Max: " . $max . "\r\n";

                    //$nations = [];
                    $nationsList = (new APICall())->call("nations");
                    echo "Count: " . count($nationsList['nations']) . "\r\n";

                    $message = "Raiding Targets\r\n------------------------\r\n";

                    foreach ($nationsList['nations'] as $target) {
                        if ($target['allianceid'] == 0) {
                            if (count($this->args) == 2 && strtolower($this->args[1]) == 'both') {
                                if ($target['score'] >= $min && $target['score'] <= $max) {
                                    if ($target['vacmode'] <= 24 && $target['vacmode'] > 0) {
                                        $wars = (new APICall())->call("war");
                                        $defenses = 0;
                                        foreach ($wars as $war) {
                                            if ($war['def_id'] == $target['nationid']) {
                                                $defenses++;
                                            }
                                        }
                                        if ($defenses < 3) {
                                            $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . " - Vacation Mode (" . $target['vacmode'] . " turns)\r\n";
                                        }
                                    } else if ($target['vacmode'] == 0 && $target['color'] == 'beige') {
                                        $data = (new APICall())->callFromWeb("https://politicsandwar.com/nation/id=" . $target['nationid']);
                                        $parse = explode("Beige Turns Left:", $data);
                                        $beige = explode("Alliance:", $parse[1]);
                                        $turns = explode(" ", $beige[0])[0];

                                        if ($turns <= 24) {
                                            $wars = (new APICall())->call("war");
                                            $defenses = 0;
                                            foreach ($wars as $war) {
                                                if ($war['def_id'] == $target['nationid']) {
                                                    $defenses++;
                                                }
                                            }
                                            if ($defenses < 3) {
                                                $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . " - Beige (" . $turns . " turns)\r\n";
                                            }
                                        }
                                    } else if ($target['vacmode'] == 0 && $target['color'] != 'beige') {
                                        $wars = (new APICall())->call("war");
                                        $defenses = 0;
                                        foreach ($wars as $war) {
                                            if ($war['def_id'] == $target['nationid']) {
                                                $defenses++;
                                            }
                                        }
                                        if ($defenses < 3) {
                                            $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . "\r\n";
                                        }
                                    }
                                }
                            } else if (count($this->args) == 2 && strtolower($this->args[1]) == 'vacation') {
                                if ($target['score'] >= $min && $target['score'] <= $max && $target['color'] != 'beige') {
                                    if ($target['vacmode'] > 0) {
                                        if ($target['vacmode'] <= 24) {
                                            $wars = (new APICall())->call("war");
                                            $defenses = 0;
                                            foreach ($wars as $war) {
                                                if ($war['def_id'] == $target['nationid']) {
                                                    $defenses++;
                                                }
                                            }
                                            if ($defenses < 3) {
                                                $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . " - Vacation Mode (" . $target['vacmode'] . " turns)\r\n";
                                            }
                                        }
                                    } else {
                                        $wars = (new APICall())->call("war");
                                        $defenses = 0;
                                        foreach ($wars as $war) {
                                            if ($war['def_id'] == $target['nationid']) {
                                                $defenses++;
                                            }
                                        }
                                        if ($defenses < 3) {
                                            $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . "\r\n";
                                        }
                                    }
                                }
                            } else if (count($this->args) == 2 && strtolower($this->args[1]) == 'beige') {
                                if ($target['score'] >= $min && $target['score'] <= $max && $target['vacmode'] == 0) {
                                    if ($target['color'] == 'beige') {
                                        $data = (new APICall())->callFromWeb("https://politicsandwar.com/nation/id=" . $target['nationid']);
                                        $parse = explode("Beige Turns Left:", $data);
                                        $beige = explode("Alliance:", $parse[1]);
                                        $turns = explode(" ", $beige[0])[0];
                                        if ($turns <= 24) {
                                            $wars = (new APICall())->call("war");
                                            $defenses = 0;
                                            foreach ($wars as $war) {
                                                if ($war['def_id'] == $target['nationid']) {
                                                    $defenses++;
                                                }
                                            }
                                            if ($defenses < 3) {
                                                $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . " - Beige (" . $turns . " turns)\r\n";
                                            }
                                        }
                                    } else {
                                        $wars = (new APICall())->call("war");
                                        $defenses = 0;
                                        foreach ($wars as $war) {
                                            if ($war['def_id'] == $target['nationid']) {
                                                $defenses++;
                                            }
                                        }
                                        if ($defenses < 3) {
                                            $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . "\r\n";
                                        }
                                    }
                                }

                            } else if (count($this->args) == 1 || strtolower($this->args[1]) == 'none') {
                                if ($target['score'] >= $min && $target['score'] <= $max && $target['color'] != 'beige' && $target['vacmode'] == 0) {
                                    $wars = (new APICall())->call("war");
                                    $defenses = 0;
                                    foreach ($wars as $war) {
                                        if ($war['def_id'] == $target['nationid']) {
                                            $defenses++;
                                        }
                                    }
                                    if ($defenses < 3) {
                                        $message .= $target['leader'] . " - https://politicsandwar.com/nation/id=" . $target['nationid'] . "\r\n";
                                    }
                                }
                            }
                        }
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
                $message = "Invalid Arguments. Must use \"!raiding <nationid> [none|beige|vacation|both]\".";

                $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                    $client->send($message, $channel);
                });
            }
        } else {
            $message = "Missing Arguments. Must use \"!raiding <nationid> [none|beige|vacation|both]\".";

            $client->getChannelGroupOrDMByID($this->channel)->then(function (ChannelInterface $channel) use ($client, $message) {
                $client->send($message, $channel);
            });
        }

    }
}