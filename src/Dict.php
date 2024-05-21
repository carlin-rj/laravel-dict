<?php
namespace Carlin\LaravelDict;

use Illuminate\Support\Facades\Cache;

class Dict
{
	private static function getCacheKey(): string
	{
		return config('dict-enum.cache-key') ?? 'dict-cache-key';
	}

    public static function getDict(): array
    {
        return Cache::store(config('dict-enum.store'))->remember(self::getCacheKey(), config('dict-enum.cache-ttl') ?? 86400, function () {
			return (new DictCollect())->collect(config('dict-enum.enum-scan-paths'));
        });
    }

    public static function getDescription(string $class, mixed $value): ?string
    {
        $dict = static::getDict();
		$list = $dict[$class]['data'] ?? [];
        foreach ($list as $item) {
            if ($item['code'] === $value) {
                return $item['name'];
            }
        }

        return null;
    }

    public static function getEnums(string $class): array
    {
        $dict = static::getDict();

        return $dict[$class]['data'] ?? [];
    }

    public static function clearDictCache(): void
    {
        Cache::store(config('dict-enum.store'))->forget(self::getCacheKey());
    }
}
