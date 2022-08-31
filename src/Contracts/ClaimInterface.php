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

use FirecmsExt\Jwt\Exceptions\InvalidClaimException;

interface ClaimInterface
{
    /**
     * Set the claim value, and call a validate method.
     *
     * @throws InvalidClaimException
     *
     * @return $this
     */
    public function setValue(mixed $value): static;

    /**
     * Get the claim value.
     */
    public function getValue(): mixed;

    /**
     * Set the claim name.
     *
     * @return $this
     */
    public function setName(string $name): static;

    /**
     * Get the claim name.
     */
    public function getName(): string;

    /**
     * Validate the Claim value.
     */
    public function validate(bool $ignoreExpired = false): bool;
}
