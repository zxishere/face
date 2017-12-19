<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

use App\Models\Subscribe;
use App\Services\StaffService;

// use App\Events\StaffCheckin;

class StaffsCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staffs:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Staffs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $subscribers = Subscribe::needPush(str_before($this->signature, ':'));
        $count =$subscribers->count();
        $dt = Carbon::now();
        foreach ($subscribers as $subscribe) {
            foreach ($this->staffService->queryData()->result as $user) {
                if ($user->attendTime != '' && $user->person == $subscribe->subscribable->name) {
                    $title = $dt->toDateString().' '.$user->attendTime.' '.$user->person.' Check In';
                    $this->call('wechat:send', [
                        'user' => $subscribe->subscriber->user_name, '--type' => 'image', '--content' => $user->attendHistoryFaceUrl
                    ]);
                    $this->call('wechat:send', [
                        'user' => $subscribe->subscriber->user_name, '--type' => 'text', '--content' => $title
                    ]);
                    $subscribe->update(['latest_push' => $dt]);
                    $count--;
                }
            }
        }
        Redis::set(str_before($this->signature, ':'), $count);
        $msg = 'Auto '.$this->description.' Done : '.date("Y-m-d H:i:s");
        Log::notice($msg);
        $this->info("\n".$msg);
    }
}
