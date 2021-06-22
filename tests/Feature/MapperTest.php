<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\Relation;
use Mockery\MockInterface;
use SebastiaanLuca\AutoMorphMap\Mapper;
use SebastiaanLuca\AutoMorphMap\Tests\MocksInstances;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class MapperTest extends TestCase
{
    use MocksInstances;

    /**
     * @test
     */
    public function it maps all models(): void
    {
        $relation = $this->getMockedRelation();

        $expected = [
            'user' => 'App\\User',
            'something_inherited' => 'App\\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'SomeThing' => 'MyPackage\\Models\\Thing',
            'different_package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once()->withNoArgs();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it doesnt map existing models(): void
    {
        $relation = $this->getMockedRelation();

        $existing = [
            'something_inherited' => 'App\\Models\\SomethingInherited',
            'thing' => 'MyPackage\\Models\\Thing',
        ];

        $expected = [
            'user' => 'App\\User',
            'address' => 'MyModule\\Models\\Address',
            'different_package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once()->withNoArgs()->andReturn($existing);
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it returns all models(): void
    {
        $models = app(Mapper::class)->getModels();

        $expected = [
            'App\\User',
            'App\\Models\\SomethingInherited',
            'MyModule\\Models\\Address',
            'MyPackage\\Models\\Sub\\Package',
            'MyPackage\\Models\\Thing',
        ];

        $this->assertSameValues($expected, $models);
    }

    /**
     * @test
     */
    public function it returns the cache path(): void
    {
        $path = app(Mapper::class)->getCachePath();

        $this->assertSame(
            base_path('bootstrap/cache/morphmap.php'),
            $path
        );
    }

    private function getMockedRelation(): MockInterface
    {
        return $this->mock('alias:'.Relation::class);
    }
}
