<?php
/**
 * SlackBot - A PHP Slack Bot.
 *
 * @package SlackBot
 * @author Keshaun Williams
 */

use Dotenv\Dotenv;
use SlackBot\SlackBot;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = new Dotenv(__DIR__);
}

error_reporting(E_ALL);

(new SlackBot())->run();