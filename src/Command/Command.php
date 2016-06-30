<?php
namespace SlackBot\Command;

use Slack\RealTimeClient;
use SlackBot\DB;
use SlackBot\Manager;
use SlackBot\Message\Message;

abstract class Command {
    protected $client;
    protected $manager;
    protected $message;
    protected $userId;
    protected $channel;
    protected $db;
    protected $args;

    public function __construct(RealTimeClient $client, Manager $manager, Message $message, DB $db, array $args = null) {
        $this->client = $client;
        $this->manager = $manager;
        $this->message = $message;
        $this->userId = $message->getUser();
        $this->channel = $message->getChannel();
        $this->db = $db;
        $this->args = $args;

        $this->init();

        echo get_called_class() . " " . $this->userId . " " . $this->channel . "\r\n";
    }

    public function init() {

    }

    public abstract function fire();
}