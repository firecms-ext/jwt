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
namespace FirecmsExt\Jwt\Contracts;

interface JwtSubjectInterface
{
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJwtIdentifier(): mixed;

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJwtCustomClaims(): array;
}
