<?php

namespace Pneves001\Apidocs\Console\Commands;

use Illuminate\Console\Command;
use Pneves001\Apidocs\Apidocs;

class GenerateApidocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apidocs:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates API documentation';

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
            // Apply the safety macros here, right before you start generating.
        // This ensures they are only active during this specific command execution.
        \Illuminate\Support\Facades\DB::macro('getTable', fn() => null);
        \Illuminate\Support\Facades\Storage::macro('getTable', fn() => null);

        $this->callSilently('route:clear');

        foreach (Apidocs::getStacks() as $name => $stack) {
            $this->info("Generating docs for stack: {$name}");

            $stackConfig = config("apidocs.stacks.{$name}", []);
            $filePath = $stackConfig['file_path'] ?? config('apidocs.file_path');
            $markdownPath = $stackConfig['markdown_file_path'] ?? config('apidocs.markdown_file_path');

            $data = $stack->export();
            file_put_contents($filePath, json_encode($data));

            $markdown = $stack->exportMarkdown();
            file_put_contents($markdownPath, $markdown);
        }

        $this->info('API docs generated');
    }
}
