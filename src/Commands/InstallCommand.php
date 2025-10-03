<?php

namespace Buderdene\FilamentWidget\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament-widget:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the widget resources';

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
     * @return int
     */
    public function handle()
    {
        $this->comment('Publishing widget Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'filament-widget-config']);

        $this->comment('Publishing Filament widget Migrations...');
        $this->callSilent('vendor:publish', ['--tag' => 'filament-widget-migrations']);
        $this->callSilent('vendor:publish', ['--tag' => 'tags-migrations']);

        $this->info('Filament widget was installed successfully.');

        return 0;
    }
}
