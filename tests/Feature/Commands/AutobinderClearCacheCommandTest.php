<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature\Commands;

use PHPUnit\Runner\Version;
use Illuminate\Contracts\Console\Kernel;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;
use SebastiaanLuca\AutoMorphMap\Commands\ClearCachedMorphMap;

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

        /**
         * assertFileDoesNotExist() was added in PHPUnit 9.1
         * assertFileNotExists() is deprecated and will be removed in PHPUnit 10
         */
        if (\version_compare(Version::id(), '9.1', '>=')) {
            $this->assertFileDoesNotExist($cache);
        } else {
            $this->assertFileNotExists($cache);
        }
    }
}
