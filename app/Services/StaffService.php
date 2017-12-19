<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;
use \Curl\Curl;
use \Curl\MultiCurl;

class StaffService
{
    private $staff;

    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
    }

    private function post($url, $data){
        $curl = new Curl();
        $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36');
        $curl->setReferrer(config('face_url').'static/attendance/index.html');
        $curl->setHeader('CLIENT-IP', '127.0.0.1');
        $curl->setHeader('X-FORWARDED-FOR', '127.0.0.1');
        $curl->post(config('face_url').$url, $data);
        $curl->close();
        if (!$curl->error) {
            return $curl->response;
        }else{
            Log::error( 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n" );
            exit;
        }
    }

    private function getSessionId(){
        $user = [
            'loginName' => config('loginName'),
            'password' => config('password')
        ];
        $response = $this->post('user/console/login', $user);
        if($response && $response->code == 'success'){
            $sessionId = $response->sessionId;
            Log::info("Get New sessionId : ". $sessionId ."!");
            Redis::set('sessionId', $sessionId);
            return $sessionId;
        }
    }

    private function checkSessionId($sessionId){
        $data = [
            'sessionId' => $sessionId
        ];
        $response = $this->post('user/console/currentUser', $data);
        Log::info("Check sessionId ".$response->code."!");
        if($response->code != 'success'){
            return $this->getSessionId();
        }else{
            return $sessionId;
        }
    }

    public function sessionId(){
        $sessionId = Redis::get('sessionId');
        return is_null($sessionId) ? $this->getSessionId() : $this->checkSessionId($sessionId);
    }

    public function queryData(){
        $dt = Carbon::now();
        $queryData = [
            'date' => $dt->toDateString(),
            'sortBy' => 'attend_time',
            'sortOrder' => 'asc',
            'lang' => 'cn',
            'sessionId' => $this->sessionId()
        ];
        $result = $this->post('user/attendance/queryByDate', $queryData);
        foreach ($result->result as $user) {
            if ($user->attendTime != ''){
                $this->staff->updateOrCreate(
                    ['name' => $user->person],
                    ['latest' => $dt->toDateString() .' '. $user->attendTime.':00']
                );
            }
        }
        return $result;
    }

    public function update(){
        $multi_curl = new MultiCurl();
        foreach ($this->queryData()->result as $user) {
            if ($user->attendTime != ''){
                $imageUrl = ($user->leaveHistoryFaceUrl != '') ? $user->leaveHistoryFaceUrl : $user->attendHistoryFaceUrl;
                $fileName = $this->staff->where('name',$user->person)->first()->id .'.jpg';
                $multi_curl->addDownload($imageUrl, storage_path('app/public/staffs/'). $fileName);
            }
        }
        $multi_curl->start();
        $multi_curl->close();
        Log::info("Multi download completed!");
    }

}
