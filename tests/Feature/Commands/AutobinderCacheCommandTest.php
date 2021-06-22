<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use SebastiaanLuca\AutoMorphMap\Commands\CacheMorphMap;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class AutobinderCacheCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it caches all models(): void
    {
        app(Kernel::class)->registerCommand(app(CacheMorphMap::class));

        $cache = base_path('bootstrap/cache/morphmap.php');

        $this->assertFileDoesNotExist($cache);

        $this->artisan('morphmap:cache');

        $this->assertFileExists($cache);

        unlink($cache);
    }
}
