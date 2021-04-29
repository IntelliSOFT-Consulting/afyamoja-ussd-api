<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Mail\MailDatabaseBackUp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
        $filename = env('DB_DATABASE')."-backup-" . Carbon::now()->format('Y-m-d') . ".sql";
        $command = "".env('DUMP_PATH')." --defaults-extra-file=~/afyamoja.cnf --databases ".env('DB_DATABASE')." --ignore-table={".env('DB_DATABASE').".password_resets,".env('DB_DATABASE').".sessions,".env('DB_DATABASE').".system_logs,".env('DB_DATABASE').".tokens,".env('DB_DATABASE').".user_tokens} > ". storage_path()."/app/backup/".$filename;
        $returnVar = null;
        $output = null;

        exec($command, $output, $returnVar);

        if (file_exists(storage_path()."/app/backup/".$filename)) {
            $to = explode(',', env('MAIL_TO'));
            Mail::to($to)->send(new MailDatabaseBackUp());
            Storage::disk('google')->put($filename, storage_path()."/app/backup/".$filename);
        }
    }
}
