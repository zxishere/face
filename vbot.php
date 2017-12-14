<?php
require_once __DIR__.'/vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Text;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$path = __DIR__.'/storage/vbot/';

$options = [
   'path'     => $path,
   /*
    * swoole 配置项（执行主动发消息命令必须要开启，且必须安装 swoole 插件）
    */
   'swoole'  => [
       'status' => true,
           'ip'     => env('SWOOLE_IP', '127.0.0.1'),
           'port'   => env('SWOOLE_PORT', 8866),
   ],
   /*
    * 下载配置项
    */
   'download' => [
       'image'         => true,
       'voice'         => false,
       'video'         => false,
       'emoticon'      => false,
       'file'          => false,
       'emoticon_path' => $path.'emoticons', // 表情库路径（PS：表情库为过滤后不重复的表情文件夹）
   ],
   /*
    * 输出配置项
    */
   'console' => [
       'output'  => true, // 是否输出
       'message' => true, // 是否输出接收消息 （若上面为 false 此处无效）
   ],
   /*
    * 日志配置项
    */
   'log'      => [
       'level'         => 'debug',
       'permission'    => 0777,
       'system'        => $path.'log', // 系统报错日志
       'message'       => $path.'log', // 消息日志
   ],
   /*
    * 缓存配置项
    */
   'cache' => [
       'default' => 'redis', // 缓存设置 （支持 redis 或 file）
       'stores'  => [
           'file' => [
               'driver' => 'file',
               'path'   => $path.'cache',
           ],
           'redis' => [
               'driver'     => 'redis',
               'connection' => 'default',
           ],
       ],
   ],
    'database' => [
        'redis' => [
            'client' => 'predis',
            'default' => [
               'host'     => env('REDIS_HOST', '127.0.0.1'),
               'password' => env('REDIS_PASSWORD', null),
               'port'     => env('REDIS_PORT', 6379),
               'database' => 2,
            ],
        ],
    ],
   /*
    * 拓展配置
    * ==============================
    * 如果加载拓展则必须加载此配置项
    */
   'extension' => [
       // 管理员配置（必选），优先加载 remark_name
       'admin' => [
           'remark'   => '',
           'nickname' => '',
       ],
   ],
];
// dd($options);
$vbot = new Vbot($options);
$vbot->messageHandler->setHandler(function ($message) {
    echo $username = $message['from']['UserName'];
    // echo $Uin = $message['from']['Uin'];
    Text::send($username, 'Hi, I\'m Vbot!');
});

$vbot->observer->setFetchContactObserver(function(array $contacts){
    print_r($contacts['friends']);
});

$vbot->server->serve();