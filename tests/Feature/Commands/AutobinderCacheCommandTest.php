<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature\Commands;

use PHPUnit\Runner\Version;
use Illuminate\Contracts\Console\Kernel;
use SebastiaanLuca\AutoMorphMap\Commands\CacheMorphMap;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class AutobinderCacheCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it caches all models() : void
    {
        app(Kernel::class)->registerCommand(app(CacheMorphMap::class));

        $cache = base_path('bootstrap/cache/morphmap.php');

        /**
         * assertFileDoesNotExist() was added in PHPUnit 9.1
         * assertFileNotExists() is deprecated and will be removed in PHPUnit 10
         */
        if (\version_compare(Version::id(), '9.1', '>=')) {
            $this->assertFileDoesNotExist($cache);
        } else {
            $this->assertFileNotExists($cache);
        }

        $this->artisan('morphmap:cache');

        $this->assertFileExists($cache);

        unlink($cache);
    }
}
