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

use FirecmsExt\Jwt\Claims\Collection;
use FirecmsExt\Jwt\Exceptions\TokenExpiredException;
use FirecmsExt\Jwt\Exceptions\TokenInvalidException;

interface PayloadValidatorInterface
{
    /**
     * Perform some checks on the value.
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     */
    public function check(Collection $value, bool $ignoreExpired = false): Collection;

    /**
     * Helper function to return a boolean.
     */
    public function isValid(Collection $value, bool $ignoreExpired = false): bool;
}
