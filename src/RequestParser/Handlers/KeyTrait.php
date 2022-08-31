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
namespace FirecmsExt\Jwt\RequestParser\Handlers;

trait KeyTrait
{
    /**
     * The key.
     */
    protected string $key = 'token';

    /**
     * Set the key.
     *
     * @return $this
     */
    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
