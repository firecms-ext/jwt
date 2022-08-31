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

use Hyperf\Utils\Collection as HyperfCollection;

class Collection extends HyperfCollection
{
    /**
     * Create a new collection.
     *
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        parent::__construct($this->getReadableItems($items));
    }

    /**
     * Get a Claim instance by it's unique name.
     */
    public function getByClaimName(string $name, ?callable $callback = null, mixed $default = null): AbstractClaim
    {
        return $this->filter(function (AbstractClaim $claim) use ($name) {
            return $claim->getName() === $name;
        })->first($callback, $default);
    }

    /**
     * Validate each claim.
     *
     * @return $this
     */
    public function validate(bool $ignoreExpired = false): static
    {
        $this->each(function ($claim) use ($ignoreExpired) {
            $claim->validate($ignoreExpired);
        });
        return $this;
    }

    /**
     * Determine if the Collection contains all the given keys.
     */
    public function hasAllClaims(mixed $claims): bool
    {
        return count($claims) and (new static($claims))->diff($this->keys())->isEmpty();
    }

    /**
     * Get the claims as key/val array.
     */
    public function toPlainArray(): array
    {
        return $this->map(function (AbstractClaim $claim) {
            return $claim->getValue();
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    protected function getReadableItems($items): array
    {
        return $this->sanitizeClaims($items);
    }

    /**
     * Ensure that the given claims array is keyed by the claim name.
     */
    private function sanitizeClaims(mixed $items): array
    {
        $claims = [];
        foreach ($items as $key => $value) {
            if (! is_string($key) and $value instanceof AbstractClaim) {
                $key = $value->getName();
            }

            $claims[$key] = $value;
        }

        return $claims;
    }
}
