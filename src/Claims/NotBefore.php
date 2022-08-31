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
namespace FirecmsExt\Jwt\Claims;

use FirecmsExt\Jwt\Exceptions\TokenInvalidException;

class NotBefore extends AbstractClaim
{
    use DatetimeTrait;

    protected string $name = 'nbf';

    /**
     * @throws TokenInvalidException
     */
    public function validate(bool $ignoreExpired = false): bool
    {
        if ($this->isFuture($this->getValue())) {
            throw new TokenInvalidException('Not Before (nbf) timestamp cannot be in the future');
        }
        return true;
    }
}
