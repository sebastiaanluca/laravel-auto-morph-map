<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use SebastiaanLuca\AutoMorphMap\Commands\ClearCachedMorphMap;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class AutobinderClearCacheCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it clears the cache() : void
    {
        app(Kernel::class)->registerCommand(app(ClearCachedMorphMap::class));

        touch($cache = base_path('bootstrap/cache/morphmap.php'));

        $this->assertFileExists($cache);

        $this->artisan('morphmap:clear');

        $this->assertFileNotExists($cache);
    }
}
