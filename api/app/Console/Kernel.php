<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CustomCommand::class,
        Commands\SendTagReport::class,
        Commands\SendFontsTagReport::class,
        Commands\SendStickerTagReport::class,
        Commands\SendTranslateTagReport::class,
        Commands\SendTagReport_V2::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('custom:command')->dailyAt('1:00');
        $schedule->command('SendTagReportMail')->weekly()->mondays()->at('4:00');
//        $schedule->command('SendFontsTagReport')->weekly()->mondays()->at('4:00');
//        $schedule->command('SendStickerTagReport')->weekly()->mondays()->at('4:00');
//        $schedule->command('SendTranslateTagReport')->weekly()->mondays()->at('4:00');
        $schedule->command('SendTagReport_V2')->weekly()->mondays()->at('4:00');

        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
