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
namespace FirecmsExt\Jwt;

use FirecmsExt\Jwt\Contracts\TokenValidatorInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Token
{
    private string $value;

    private TokenValidatorInterface $validator;

    /**
     * Create a new JSON Web Token.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(string $value)
    {
        $this->validator = ApplicationContext::getContainer()->get(TokenValidatorInterface::class);
        $this->value = (string) $this->validator->check($value);
    }

    /**
     * Get the token when casting to string.
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Get the token.
     */
    public function get(): string
    {
        return $this->value;
    }
}
