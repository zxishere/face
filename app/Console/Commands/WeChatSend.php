<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use \Curl\Curl;

class WeChatSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wechat:send {user} {--type=} {--content=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'We Chat Send Message';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function post($action, $params){
        $data = [
            'action' => $action,
            'params' => $params
        ];
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post(env('SWOOLE_IP').':'.env('SWOOLE_PORT'), $data);
        $curl->close();
        if (!$curl->error) {
            return $curl->response;
        }else{
            die( 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n" );
        }

    }

/*    private function getUsername($name){
        $params = [
            'type' => 'friends',
            'method' => 'getUsername',
            'filter' => [$name, "NickName", false, true]
        ];
        $response = $this->post('search', $params);
        if($response->result->friends){
            return $response->result->friends;
        }else{
            die( 'friends return bool');
        }
    }*/

    private function send($type, $username, $content){
        $params = [
            'type' => $type,
            // 'username' => $this->getUsername($username),
            'username' => $username,
            'content' => $content
        ];
        if ($type == 'image') {
            $curl = new Curl();
            $response = (object) [];
            $curl->download($content, function ($instance, $tmpfile) use (&$params, &$response){
                $params['content'] = stream_get_meta_data($tmpfile)['uri'];
                $response = $this->post('send', $params);
            });
            return $response;
        }else{
            return $this->post('send', $params);
        }

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->send($this->option('type'), $this->argument('user'), $this->option('content'));
    }
}
