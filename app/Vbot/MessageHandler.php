<?php

namespace App\Vbot;

use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class MessageHandler
{
    public static function messageHandler(Collection $message)
    {

        echo $message['type'];
        echo $username = $message['from']['UserName'];
        // echo $Uin = $message['from']['Uin'];
        Text::send($username, 'Hi, I\'m Vbot!');

    }
}
