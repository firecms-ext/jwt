<?php

declare(strict_types=1);
/**
 * This file is part of FirecmsExt JWT.
 *
 * @link     https://www.klmis.cn
 * @document https://www.klmis.cn
 * @contact  zhimengxingyun@klmis.cn
 * @license  https://github.com/firecms-ext/jwt/blob/master/LICENSE
 */
namespace FirecmsExt\Jwt\Contracts;

interface StorageInterface
{
    public function add(string $key, mixed $value, int $ttl);

    public function forever(string $key, mixed $value);

    public function get(string $key): mixed;

    public function destroy(string $key): bool;

    public function flush(): void;
}
