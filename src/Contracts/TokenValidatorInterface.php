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

interface TokenValidatorInterface extends ValidatorInterface
{
    /**
     * Perform some checks on the value.
     */
    public function check(string $value): string;

    /**
     * Helper function to return a boolean.
     */
    public function isValid(string $value): bool;
}
