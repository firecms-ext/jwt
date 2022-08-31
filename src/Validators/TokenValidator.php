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
namespace FirecmsExt\Jwt\Validators;

use FirecmsExt\Jwt\Contracts\TokenValidatorInterface;
use FirecmsExt\Jwt\Exceptions\JwtException;
use FirecmsExt\Jwt\Exceptions\TokenInvalidException;

class TokenValidator implements TokenValidatorInterface
{
    /**
     * Check the structure of the token.
     *
     * @throws TokenInvalidException
     */
    public function check(string $value): string
    {
        $this->validateStructure($value);
        return $value;
    }

    /**
     * Helper function to return a boolean.
     */
    public function isValid(string $value): bool
    {
        try {
            $this->check($value);
        } catch (JwtException $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws TokenInvalidException
     */
    protected function validateStructure(string $token): static
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new TokenInvalidException('Wrong number of segments');
        }

        $parts = array_filter(array_map('trim', $parts));

        if (count($parts) !== 3 or implode('.', $parts) !== $token) {
            throw new TokenInvalidException('Malformed token');
        }

        return $this;
    }
}
