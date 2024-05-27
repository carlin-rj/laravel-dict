<?php

namespace Carlin\LaravelDict\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class EnumClass
{
    public function __construct(
        public string $name,
        public string $description = '',
		public ?string $group = null,
		public array $options = [],
	) {
    }
}
