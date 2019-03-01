<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use SebastiaanLuca\AutoMorphMap\Constants\CaseTypes;
use SebastiaanLuca\AutoMorphMap\Constants\NamingSchemes;
use SebastiaanLuca\AutoMorphMap\Mapper;
use SebastiaanLuca\AutoMorphMap\Tests\MocksInstances;
use SebastiaanLuca\AutoMorphMap\Tests\TestCase;

class AutobinderNameOverrideTest extends TestCase
{
    use MocksInstances;

    /**
     * @test
     */
    public function it maps all models using the user defined method() : void
    {
        $relation = $this->getMockedRelation();

        config()->set('auto-morph-map.naming', NamingSchemes::TABLE_NAME);
        config()->set('auto-morph-map.case', CaseTypes::SLUG_CASE);

        config()->set('auto-morph-map.conversion', function (string $model) {
            return 'prefixed_' . Str::snake(class_basename($model));
        });

        $expected = [
            'prefixed_user' => 'App\\User',
            'prefixed_something_inherited' => 'App\Models\\SomethingInherited',
            'prefixed_address' => 'MyModule\\Models\\Address',
            'prefixed_thing' => 'MyPackage\\Models\\Thing',
            'prefixed_package' => 'MyPackage\\Models\\Sub\\Package',
        ];

        $relation->shouldReceive('morphMap')->once()->withNoArgs();
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
