<?php
namespace SlackBot;

use Slack\Channel;
use Slack\Payload;
use Slack\RealTimeClient;

class SlackRTMClient extends RealTimeClient {
    public function refreshChannel($channelId) {
        $this->apiCall('channels.info', [
            'channel' => $channelId,
        ])->then(function (Payload $response) {
            $channel = new Channel($this, $response['channel']);
            $this->channels[$channel->getId()] = $channel;
        });
    }
}