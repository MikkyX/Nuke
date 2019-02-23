<?php

namespace MikkyX\Nuke\Commands;

use App;
use Illuminate\Console\Command;
use Storage;

class Nuke extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nuke';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take your website back to day zero... and rebuild.';

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
        if ($this->runningOnProduction()) {
            $this->error('You can\'t run this in production!');
            return false;
        }

        if (!$this->confirmExecution()) {
            $this->error('Confirmation not received. Aborting...');
            return false;
        }

        $this->migrateFresh();
        $this->runSeeders();
        $this->clearApplicationCache();
        $this->clearConfigurationCache();
        $this->deleteStorage();
        $this->generateApplicationKey();

        $this->info('Done!');

        return true;
    }

    private function clearApplicationCache()
    {
        $this->info('Clearing application cache...');
        $this->callSilent('cache:clear');
    }

    private function clearConfigurationCache()
    {
        $this->info('Clearing config cache...');
        $this->callSilent('config:clear');
    }

    private function confirmExecution()
    {
        $this->error('**********************************************************');
        $this->error('* Do you really want to nuke the entire site from orbit? *');
        $this->error('**********************************************************');

        return $this->ask('Type \'It\'s the only way to be sure\' to confirm')
            == 'It\'s the only way to be sure';
    }

    private function deleteStorage()
    {
        $this->info('Clearing out public disk...');

        foreach (Storage::disk('public')->directories('.') as $dir) {
            Storage::disk('public')->deleteDirectory($dir);
        }

        $files = collect(Storage::disk('public')->allFiles('.'))
            ->reject(function ($item) {
                return $item == '.gitignore';
            });

        Storage::disk('public')->delete($files->toArray());
    }

    private function generateApplicationKey()
    {
        $this->info('Regenerating application key...');
        $this->callSilent('key:generate', [
            '--ansi'
        ]);
    }

    private function migrateFresh()
    {
        $this->info('Migrating fresh database...');
        $this->callSilent('migrate:fresh', [
            '--force'
        ]);
    }

    private function runningOnProduction()
    {
        return App::environment(['live', 'master', 'production']);
    }

    private function runSeeders()
    {
        $this->info('Running database seeders...');
        $this->callSilent('db:seed');
    }
}
