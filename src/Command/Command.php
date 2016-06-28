<?php
namespace SlackBot\Command;

use Slack\RealTimeClient;
use SlackBot\Manager;
use SlackBot\Message\Message;

abstract class Command {
    protected $client;
    protected $manager;
    protected $message;
    protected $userId;
    protected $channel;
    protected $args;

    public function __construct(RealTimeClient $client, Manager $manager, Message $message, array $args = null) {
        $this->client = $client;
        $this->manager = $manager;
        $this->message = $message;
        $this->userId = $message->getUser();
        $this->channel = $message->getChannel();
        $this->args = $args;

        $this->init();

        echo get_called_class() . ' ' . $this->userId . ' ' . $this->channel . '\r\n';
    }

    public function init() {

    }

    public abstract function fire();
}