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

class Issuer extends AbstractClaim
{
    protected string $name = 'iss';

    public function validate(bool $ignoreExpired = false): bool
    {
        return true;
    }
}