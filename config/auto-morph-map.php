<?php

use SebastiaanLuca\AutoMorphMap\Constants\CaseTypes;
use SebastiaanLuca\AutoMorphMap\Constants\NamingSchemes;

return [

    /**
     * The naming scheme to use when determining the model's morph type
     * base value. Defaults to singular table name.
     */
    'naming' => NamingSchemes::SINGULAR_TABLE_NAME,

    /**
     * The case type to use when aliasing a model. Defaults to use no conversion.
     */
    'case' => CaseTypes::NONE,

    /**
     * If you wish, you can override the naming and conversion altogether
     * using a closure.
     */
    //    'conversion' => function (string $model) {
    //        return snake_case(basename($model));
    //    },

];
