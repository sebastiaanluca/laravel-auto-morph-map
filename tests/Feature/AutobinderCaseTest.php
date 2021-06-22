<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\Relation;
use Mockery\MockInterface;
use SebastiaanLuca\AutoMorphMap\Constants\CaseTypes;
use SebastiaanLuca\AutoMorphMap\Mapper;
use SebastiaanLuca\AutoMorphMap\Tests\MocksInstances;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class AutobinderCaseTest extends TestCase
{
    use MocksInstances;

    /**
     * @test
     */
    public function it maps all models using the default case(): void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.case', null);

        $expected = [
            'user' => 'App\\User',
            'something_inherited' => 'App\Models\\SomethingInherited',
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
    public function it maps all models using snake case(): void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.case', CaseTypes::SNAKE_CASE);

        $expected = [
            'user' => 'App\\User',
            'something_inherited' => 'App\\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'some_thing' => 'MyPackage\\Models\\Thing',
            'different_package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once()->withNoArgs();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it maps all models using slug case(): void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.case', CaseTypes::SLUG_CASE);

        $expected = [
            'user' => 'App\\User',
            'something-inherited' => 'App\\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'something' => 'MyPackage\\Models\\Thing',
            'different-package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once()->withNoArgs();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it maps all models using camel case(): void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.case', CaseTypes::CAMEL_CASE);

        $expected = [
            'user' => 'App\\User',
            'somethingInherited' => 'App\\Models\\SomethingInherited',
            'address' => 'MyModule\\Models\\Address',
            'someThing' => 'MyPackage\\Models\\Thing',
            'differentPackage' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once()->withNoArgs();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    /**
     * @test
     */
    public function it maps all models using studly case(): void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.case', CaseTypes::STUDLY_CASE);

        $expected = [
            'User' => 'App\\User',
            'SomethingInherited' => 'App\\Models\\SomethingInherited',
            'Address' => 'MyModule\\Models\\Address',
            'SomeThing' => 'MyPackage\\Models\\Thing',
            'DifferentPackage' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once()->withNoArgs();
        $relation->shouldReceive('morphMap')->once()->with($expected);

        app(Mapper::class)->map();
    }

    private function getMockedRelation(): MockInterface
    {
        return $this->mock('alias:'.Relation::class);
    }
}
