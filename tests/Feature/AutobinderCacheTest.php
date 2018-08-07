<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\Relation;
use Mockery\MockInterface;
use SebastiaanLuca\AutoMorphMap\Mapper;
use SebastiaanLuca\AutoMorphMap\Tests\MocksInstances;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class AutobinderCacheTest extends TestCase
{
    use MocksInstances;

    /**
     * @test
     */
    public function it doesnt read from cache when not cached() : void
    {
        $relation = $this->getMockedRelation();

        $expected = [
            'user' => 'App\\User',
            'something_inherited' => 'App\\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'thing' => 'MyPackage\\Models\\Thing',
            'package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it reads from cache when cached() : void
    {
        $cache = base_path('bootstrap/cache/morphmap.php');

        $copy = copy(
            base_path('cache.php'),
            $cache
        );

        $this->assertTrue($copy);

        $relation = $this->getMockedRelation();

        $expected = [
            'something_cached' => 'App\\SomethingCached',
            'cached_user' => 'App\\Models\\CachedUser',
        ];

        $relation->shouldReceive('morphMap')->once();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();

        unlink($cache);
    }

    /**
     * @return \Mockery\MockInterface
     */
    private function getMockedRelation() : MockInterface
    {
        return $this->mock('alias:' . Relation::class);
    }
}
