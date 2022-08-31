<?php

declare(strict_types=1);
/**
 * This file is part of FirecmsExt JWT.
 *
 * @link     https://www.klmis.cn
 * @document https://www.klmis.cn
 * @contact  zhimengxingyun@klmis.cn
 * @license  https://gitee.com/firecms-ext/jwt/blob/master/LICENSE
 */
namespace FirecmsExt\Jwt\Storage;

use FirecmsExt\Jwt\Contracts\StorageInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class HyperfCache implements StorageInterface
{
    /**
     * The cache repository contract.
     */
    protected CacheInterface $cache;

    /**
     * The used cache tag.
     */
    protected string $tag;

    /**
     * Constructor.
     */
    public function __construct(CacheInterface $cache, string $tag)
    {
        $this->cache = $cache;
        $this->tag = $tag;
    }

    public function add(string $key, mixed $value, int $ttl)
    {
        $this->cache->set($this->resolveKey($key), $value, $ttl);
    }

    public function forever(string $key, mixed $value)
    {
        $this->cache->set($this->resolveKey($key), $value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $key): mixed
    {
        return $this->cache->get($this->resolveKey($key));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function destroy(string $key): bool
    {
        return $this->cache->delete($this->resolveKey($key));
    }

    public function flush(): void
    {
        method_exists($cache = $this->cache, 'clearPrefix')
            ? $cache->clearPrefix($this->tag)
            : $cache->clear();
    }

    protected function cache(): CacheInterface
    {
        return $this->cache;
    }

    protected function resolveKey(string $key): string
    {
        return $this->tag . '.' . $key;
    }
}
