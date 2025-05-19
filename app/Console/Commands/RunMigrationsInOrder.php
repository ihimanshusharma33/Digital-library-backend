<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunMigrationsInOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-migrations-in-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('migrate --path=database/migrations/2025_05_12_120933_create_courses_table.php');
        Artisan::call('migrate --path=database/migrations/0001_01_01_000000_create_users_table.php');
        Artisan::call('migrate --path=database/migrations/0001_01_01_000001_create_cache_table.php');
        Artisan::call('migrate --path=database/migrations/0001_01_01_000002_create_jobs_table.php');
        Artisan::call('migrate --path=database/migrations/2025_05_12_120355_create_books_table.php');
        Artisan::call('migrate --path=database/migrations/2025_05_12_120413_create_ebooks_table.php');
        Artisan::call('migrate --path=database/migrations/2025_05_12_120420_create_notes_table.php'); 
        Artisan::call('migrate --path=database/migrations/2025_05_12_120442_create_question_papers_table.php');
        Artisan::call('migrate --path=database/migrations/2025_05_12_120457_create_notifications_table.php');
        Artisan::call('migrate --path=database/migrations/2025_05_12_120518_create_issued_books_table.php');    
        Artisan::call('migrate --path=database/migrations/2025_05_18_081751_create_password_reset_otps_table.php');    
    }
}
