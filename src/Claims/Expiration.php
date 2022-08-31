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
namespace FirecmsExt\Jwt\Claims;

use FirecmsExt\Jwt\Exceptions\TokenExpiredException;

class Expiration extends AbstractClaim
{
    use DatetimeTrait;

    protected string $name = 'exp';

    /**
     * @throws TokenExpiredException
     */
    public function validate(bool $ignoreExpired = false): bool
    {
        if (! $ignoreExpired and $this->isPast($this->getValue())) {
            throw new TokenExpiredException('Token has expired');
        }
        return true;
    }
}
