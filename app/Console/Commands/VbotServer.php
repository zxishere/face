<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Text;
use App\Vbot\Observer;
use App\Vbot\MessageHandler;

class VbotServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vbot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Vbot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vbot = new Vbot(config('vbot'));
        $vbot->messageHandler->setHandler([MessageHandler::class, 'messageHandler']);
        $vbot->observer->setQrCodeObserver([Observer::class, 'setQrCodeObserver']);
        $vbot->observer->setLoginSuccessObserver([Observer::class, 'setLoginSuccessObserver']);
        $vbot->observer->setReLoginSuccessObserver([Observer::class, 'setReLoginSuccessObserver']);
        $vbot->observer->setExitObserver([Observer::class, 'setExitObserver']);
        $vbot->observer->setFetchContactObserver([Observer::class, 'setFetchContactObserver']);
        $vbot->observer->setBeforeMessageObserver([Observer::class, 'setBeforeMessageObserver']);
        $vbot->observer->setNeedActivateObserver([Observer::class, 'setNeedActivateObserver']);
        $vbot->server->serve();

/*        $vbot->messageHandler->setHandler(function ($message) {
            echo $username = $message['from']['UserName'];
            // echo $Uin = $message['from']['Uin'];
            Text::send($username, 'Hi, I\'m Vbot from artisan!');
        });
        $vbot->server->serve();*/
    }
}
