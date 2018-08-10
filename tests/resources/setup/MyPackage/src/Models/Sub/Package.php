<?php

declare(strict_types=1);

namespace MyPackage\Models\Sub;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'different_packages';
}
