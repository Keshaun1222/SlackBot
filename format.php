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

//$db = new DB();

$text = "<!here>";
$send = true;

/*$wars = (new APICall())->call('war');
foreach ($wars as $war) {
    if ($war['def_ally_id'] == 1584) {
        $check = $db->query('waralert', array('timeline' => 'int:' . $war['timeline_id']));
        if (count($check) == 0) {
            $text .= "<https://politicsandwar.com/nation/id=" . $war['def_id'] . "|" . $war['def_name'] . "> is being attacked by <https://politicsandwar.com/nation/id=" . $war['atk_id'] . "|" . $war['atk_name'] . ">. Timeline: https://politicsandwar.com/nation/war/timeline/war=" . $war['timeline_id'] . "\r\n";
            $send = true;
            $insert = $db->insert('waralert', array('timeline'), array('int' => $war['timeline_id']));
            if (!$insert) {
                echo $db->error() . "\r\n";
            }
        }
    }
}*/
if ($send) {
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
}
echo "Script ran accordingly\r\n";