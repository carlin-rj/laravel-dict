<?php

namespace Carlin\LaravelDict\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class EnumProperty
{
    public function __construct(
        public string $description,
        public array $options = [],
    ) {
    }
}
