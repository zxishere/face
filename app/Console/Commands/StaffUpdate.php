<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\StaffService;

class StaffUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Staff';

    protected $staffService;

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
        $this->staffService->update(true);
        $msg = 'Auto '.$this->description.' Done : '.date("Y-m-d H:i:s");
        Log::notice($msg);
        $this->info("\n".$msg);
    }
}
