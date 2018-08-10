<?php

declare(strict_types=1);

namespace MyPackage\Models;

use Illuminate\Database\Eloquent\Model;

class Thing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'SomeThings';
}
