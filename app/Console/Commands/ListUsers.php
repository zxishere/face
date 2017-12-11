<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use \Curl\Curl;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:list {--sessionId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List All Users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function post($url, $data){
        $curl = new Curl();
        $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36');
        $curl->setReferrer(env('DOMAIN').'static/attendance/index.html');
        $curl->setHeader('CLIENT-IP', env('FACK_IP'));
        $curl->setHeader('X-FORWARDED-FOR', env('FACK_IP'));
        $curl->post(env('DOMAIN').$url, $data);
        $curl->close();
        if (!$curl->error) {
            return $curl->response;
        }else{
            die( 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n" );
        }
    }


    private function getSessionId(){
        $user = [
            'loginName' => env('LOGIN_NAME'),
            'password' => env('LOGIN_PASSWORD')
        ];
        $response = $this->post('user/console/login', $user);
        if($response && $response->code == 'success'){
            $sessionId = $response->sessionId;
            $this->info("Get New sessionId : ". $sessionId ."!");
            Cache::forever('sessionId', $sessionId);
            return $sessionId;
        }
    }

    private function checkSessionId($sessionId){
        $data = [
            'sessionId' => $sessionId
        ];
        $response = $this->post('user/console/currentUser', $data);
        $this->info("Check sessionId ".$response->code."!");
        if($response->code != 'success'){
            return $this->getSessionId();
        }else{
            return $sessionId;
        }
    }


    private function scSend($text, $desp = '')
    {
        $curl = new Curl();
        $curl->post('https://sc.ftqq.com/'.env('KEY').'.send', [
            'text' => $text,
            'desp' => $desp
        ]);
        $curl->close();
        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            exit;
        }else {
            return $curl->response;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
/*        $this->call('wechat:send', [
            'user' => env('WECHAT_USER'), '--type' => 'text', '--content' => '111'
        ]);
        exit;*/
        if($this->option('sessionId')){
            if (Cache::has('sessionId')) {
                $sessionId = $this->checkSessionId(Cache::get('sessionId'));
            }else{
                $sessionId = $this->getSessionId();
            }
            echo $sessionId;
            exit;
        }
        $focusUsers = env('FOCUS_USERS') ? array_filter(explode(',', env('FOCUS_USERS'))) : [];
        $dt = Carbon::now();
        $this->info('Start:'.$dt);
        $expiresAt = (new Carbon)->diffInMinutes(Carbon::createFromTime(22, 0, 0), true);
        if(date("H:i") == '07:01' || !Cache::has('focusUsers')){
            Cache::forget('focusUsers');
            $this->info("Init Users!");
            Cache::put('focusUsers', $focusUsers, $expiresAt);
        }
        $cacheFocusUsers = Cache::get('focusUsers');
        if(!empty($cacheFocusUsers)){
            if (Cache::has('sessionId')) {
                $sessionId = $this->checkSessionId(Cache::get('sessionId'));
            }else{
                $sessionId = $this->getSessionId();
            }
            $queryData = [
                'date' => $dt->toDateString(),
                'sortBy' => 'attend_time',
                'sortOrder' => 'asc',
                'lang' => 'cn',
                'sessionId' => $sessionId
            ];
            $queryResult = $this->post('user/attendance/queryByDate', $queryData);
            foreach ($queryResult->result as $user) {
                if ($user->attendTime != '' && in_array($user->person, $cacheFocusUsers)) {
                    $title = $dt->toDateString().' '.$user->attendTime.' '.$user->person.' Check In';
                    $this->call('wechat:send', [
                        'user' => env('WECHAT_USER'), '--type' => 'image', '--content' => $user->attendHistoryFaceUrl
                    ]);
                    $this->call('wechat:send', [
                        'user' => env('WECHAT_USER'), '--type' => 'text', '--content' => $title
                    ]);
                    /*
                    $img = str_replace(env('DOMAIN'), env('REPLACE_DOMAIN'), $user->attendHistoryFaceUrl);
                    $desc = '![]('.$img.')';
                    $this->scSend($title, $desc);
                    */
                    if (($key = array_search($user->person, $cacheFocusUsers)) !== false) {
                        unset($cacheFocusUsers[$key]);
                    }
                }
            }
            Cache::put('focusUsers', $cacheFocusUsers, $expiresAt);
        }
    }
}
