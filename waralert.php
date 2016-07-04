<?php
/**
 * Created by PhpStorm.
 * User: Keshaun
 * Date: 7/4/2016
 * Time: 12:54 PM
 */

use Dotenv\Dotenv;
use SlackBot\DB;
use SlackBot\APICall;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();
}

error_reporting(E_ALL);

$db = new DB();

$text = "Testing!\r\nHow are you?\r\n";

$params = array(
    'token' => getenv('TOKEN'),
    'channel' => 'test',
    'text' => $text,
    'as_user' => 'true'
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://slack.com/api/chat.postMessage');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'Crimson Bot');
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
curl_exec($curl);
curl_close($curl);