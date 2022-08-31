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

use DateInterval;
use DateTimeInterface;
use FirecmsExt\Jwt\Exceptions\InvalidClaimException;
use FirecmsExt\Jwt\Utils;

trait DatetimeTrait
{
    /**
     * Time leeway in seconds.
     */
    protected int $leeway = 0;

    /**
     * Set the claim value, and call a validate method.
     * @return $this
     */
    public function setValue(mixed $value): static
    {
        if ($value instanceof DateInterval) {
            $value = Utils::now()->add($value);
        }

        if ($value instanceof DateTimeInterface) {
            $value = $value->getTimestamp();
        }

        return parent::setValue($value);
    }

    /**
     * @throws InvalidClaimException
     */
    public function validateCreate(mixed $value): float|int|string
    {
        if (! is_numeric($value)) {
            throw new InvalidClaimException($this);
        }

        return $value;
    }

    /**
     * Set the leeway in seconds.
     *
     * @return $this
     */
    public function setLeeway(int $leeway): static
    {
        $this->leeway = $leeway;

        return $this;
    }

    /**
     * Determine whether the value is in the future.
     */
    protected function isFuture(mixed $value): bool
    {
        return Utils::isFuture((int) $value, (int) $this->leeway);
    }

    /**
     * Determine whether the value is in the past.
     */
    protected function isPast(mixed $value): bool
    {
        return Utils::isPast((int) $value, (int) $this->leeway);
    }
}
