<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Buat file Service baru';

    public function handle(Filesystem $files)
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (!$files->isDirectory(app_path('Services'))) {
            $files->makeDirectory(app_path('Services'), 0755, true);
        }

        if ($files->exists($path)) {
            $this->error("Service {$name} sudah ada!");
            return;
        }

        $files->put($path, $this->template($name));
        $this->info("Service {$name} berhasil dibuat: app/Services/{$name}.php");
    }

    private function template(string $name): string
    {
        return <<<PHP
        <?php

        namespace App\Services;

        class {$name}
        {
            //
        }
        PHP;
    }
}