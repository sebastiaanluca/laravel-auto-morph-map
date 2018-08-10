<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\Relation;
use Mockery\MockInterface;
use SebastiaanLuca\AutoMorphMap\Constants\NamingSchemes;
use SebastiaanLuca\AutoMorphMap\Mapper;
use SebastiaanLuca\AutoMorphMap\Tests\MocksInstances;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class AutobinderNamingTest extends TestCase
{
    use MocksInstances;

    /**
     * @test
     */
    public function it maps all models using the default naming scheme() : void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.naming', null);

        $expected = [
            'user' => 'App\\User',
            'something_inherited' => 'App\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'thing' => 'MyPackage\\Models\\Thing',
            'different_package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it maps all models using the singular table name naming scheme() : void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.naming', NamingSchemes::SINGULAR_TABLE_NAME);

        $expected = [
            'user' => 'App\\User',
            'something_inherited' => 'App\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'thing' => 'MyPackage\\Models\\Thing',
            'different_package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it maps all models using the table name naming scheme() : void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.naming', NamingSchemes::TABLE_NAME);

        $expected = [
            'users' => 'App\\User',
            'something_inheriteds' => 'App\Models\\SomethingInherited',
            'addresses' => 'MyModule\\Models\\Address',
            'things' => 'MyPackage\\Models\\Thing',
            'different_packages' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it maps all models using the class basename naming scheme() : void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.naming', NamingSchemes::CLASS_BASENAME);

        $expected = [
            'user' => 'App\\User',
            'something_inherited' => 'App\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'thing' => 'MyPackage\\Models\\Thing',
            'package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @return \Mockery\MockInterface
     */
    private function getMockedRelation() : MockInterface
    {
        return $this->mock('alias:' . Relation::class);
    }
}
