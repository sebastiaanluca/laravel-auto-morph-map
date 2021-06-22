<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Commands;

use Illuminate\Console\Command;
use SebastiaanLuca\AutoMorphMap\Mapper;

class ClearCachedMorphMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'morphmap:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the morph map cache file';

    private Mapper $mapper;

    public function __construct(Mapper $mapper)
    {
        parent::__construct();

        $this->mapper = $mapper;
    }

    public function handle(): void
    {
        @unlink($this->mapper->getCachePath());

        $this->info('Morph map models cache cleared!');
    }
}
