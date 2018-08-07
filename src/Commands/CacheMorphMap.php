<?php

namespace SebastiaanLuca\AutoMorphMap\Commands;

use Illuminate\Console\Command;
use SebastiaanLuca\AutoMorphMap\Mapper;

class CacheMorphMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'morphmap:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster morph mapping';

    /**
     * @var \SebastiaanLuca\AutoMorphMap\Mapper
     */
    private $mapper;

    /**
     * Create a new command instance.
     *
     * @param \SebastiaanLuca\AutoMorphMap\Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        parent::__construct();

        $this->mapper = $mapper;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        $models = $this->mapper->getModels();

        $cache = $this->mapper->getCachePath();

        file_put_contents(
            $cache,
            '<?php return ' . var_export($models, true) . ';'
        );

        $this->info('Morph map models cached!');
    }
}
