<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $filename = env('APP_ENV')."backup-" . Carbon::now()->format('Y-m-d') . ".sql";
        $command = "".env('DUMP_PATH')." --defaults-extra-file=~/afyamoja.cnf --databases ".env('DB_DATABASE')."  > " . storage_path() . "/app/backup/" . $filename;
        $returnVar = null;
        $output = null;

        exec($command, $output, $returnVar);
    }
}
