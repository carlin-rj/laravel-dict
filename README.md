# laravel-dict
Scan the specified path to obtain enumeration information through reflection

### 安装:
composer require carlin/laravel-dict

### 发布配置文件:
php artisan vendor:publish --provider "Carlin\LaravelDict\DictServiceProvider"

### 配置文件:
```php
<?php
return [
	'store'=>env('DICT_ENUM_STORE', env('CACHE_DRIVER', 'file')), //缓存驱动
	'cache-key'=>env('DICT_ENUM_CACHE_KEY', 'dict-cache-key'), //缓存key
	'cache-ttl'=> (int)env('DICT_ENUM_CACHE_TTL', 60 * 60 * 24 * 30), //缓存时间, 默认30天
	'enum-scan-paths'=>[
		//base_path('app/Enums/*.php'), //扫描路径
	],
];
```

### 例子:
```php
<?php
namespace App\Enums;

use Carlin\LaravelDict\Dict;
use BenSampo\Enum\Enum;

class BaseEnum extends Enum {
    public static function getDescription(mixed $value): string
    {
        return Dict::getDescription(static::class, $value) ?? parent::getDescription($value);
    }

    public static function descriptions(): array
    {
        return Dict::getEnums(static::class);
    }
}

```
```php
<?php

namespace App\Enums;

use Carlin\LaravelDict\Attributes\EnumClass;
use Carlin\LaravelDict\Attributes\EnumProperty;

#[EnumClass(__CLASS__, '布尔整型枚举', 'webApi')] //枚举类注解
class BoolIntEnums extends BaseEnum
{
    //#[EnumProperty('是', ['test'=>2])] //拓展test字段
    #[EnumProperty('是', ['test'=>2])]
    public const TRUE = 1;

    //#[EnumProperty('否', ['test'=>1])]
    #[EnumProperty('否')]
    public const FALSE = 0;
}

```

```php
<?php
use Carlin\LaravelDict\Dict;
//清除缓存
Dict::clearDictCache();
//获取枚举信息
Dict::getEnums(BoolIntEnums::class); //获取枚举信息
//getDescription
Dict::getDescription(BoolIntEnums::class, 1); //获取枚举描述
//获取枚举字典列表
Dict::getDict();
//获取指定group字典
Dict::getByGroup('webApi');
```

### Dict::getDict输出示例:
```
//获取枚举字典列表
Array
(
    [App\Enums\BoolIntEnum] => Array
        (
            [name] => bool
            [description] => 布尔值字典
            [group] => webapi
            [data] => Array
                (
                    [0] => Array
                        (
                            [name] => 是
                            [code] => 1
                        )

                    [1] => Array
                        (
                            [name] => 否
                            [code] => 0
                        )

                )

            [class] => App\Enums\BoolIntEnum
        )
)
```
