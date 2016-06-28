<?php
namespace SlackBot\Message;

class Message {
    private $text;
    private $channel;
    private $user;
    private $subType;

    public function __construct($data = []) {
        $this->text = isset($data['text']) ? $data['text'] : null;
        $this->channel = isset($data['channel']) ? $data['channel'] : null;
        $this->user = isset($data['user']) ? $data['user'] : null;
        $this->subType = isset($data['subtype']) ? $data['subtype'] : null;
    }

    public function getText() {
        return $this->text;
    }

    public function getSubType() {
        return $this->subType;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getChannel() {
        return $this->channel;
    }

    public function setChannel($channel) {
        $this->channel = $channel;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }
}