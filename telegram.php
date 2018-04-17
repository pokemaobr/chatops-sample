<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use TelegramBot\Api\BotApi;
use GuzzleHttp\Client;

$client = new Client();
$url = 'http://pokephp.com.br/chatops';

try 
{
$res = $client->request('GET', $url);
$responseCode = $res->getStatusCode();
} catch (GuzzleHttp\Exception\ClientException $e){
$responseCode = '404';
}

if ($responseCode != '200') 
{

$bot = new BotApi($botId);
$bot->sendMessage($chatId, 'O sistema: ' . $url . ' está fora do ar');

}