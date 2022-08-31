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

use FirecmsExt\Jwt\Claims\Collection;
use FirecmsExt\Jwt\Claims\Factory as ClaimFactory;
use FirecmsExt\Jwt\Contracts\ClaimInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PayloadFactory
{
    /**
     * The claim factory.
     */
    protected ClaimFactory $claimFactory;

    /**
     * The default claims.
     */
    protected array $defaultClaims = [
        'iss',
        'iat',
        'exp',
        'nbf',
        'jti',
    ];

    public function __construct(ClaimFactory $claimFactory)
    {
        $this->claimFactory = $claimFactory;
    }

    /**
     * Create the Payload instance.
     *
     * @throws Exceptions\TokenExpiredException
     * @throws Exceptions\TokenInvalidException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function make(array $claims, bool $ignoreExpired = false): Payload
    {
        return new Payload($this->resolveClaims($this->buildClaims($claims)), $ignoreExpired);
    }

    /**
     * Set the default claims to be added to the Payload.
     *
     * @return $this
     */
    public function setDefaultClaims(array $claims): static
    {
        $this->defaultClaims = $claims;

        return $this;
    }

    /**
     * Get the default claims.
     *
     * @return string[]
     */
    public function getDefaultClaims(): array
    {
        return $this->defaultClaims;
    }

    /**
     * Helper to set the ttl.
     *
     * @return $this
     */
    public function setTtl(int $ttl): static
    {
        $this->claimFactory->setTtl($ttl);

        return $this;
    }

    /**
     * Helper to get the ttl.
     */
    public function getTtl(): int
    {
        return $this->claimFactory->getTtl();
    }

    /**
     * Build the default claims.
     */
    protected function buildClaims(array $claims): Collection
    {
        $collection = new Collection();
        $defaultClaims = $this->getDefaultClaims();

        // remove the exp claim if it exists and the ttl is null
        if ($this->claimFactory->getTtl() === null and $key = array_search('exp', $defaultClaims)) {
            unset($defaultClaims[$key]);
        }

        // add the default claims
        foreach ($defaultClaims as $claim) {
            $collection->put($claim, $this->claimFactory->make($claim));
        }

        // add custom claims on top, allowing them to overwrite defaults
        foreach ($claims as $name => $value) {
            $collection->put($name, $value);
        }

        return $collection;
    }

    /**
     * Build out the Claim DTO's.
     */
    protected function resolveClaims(Collection $claims): Collection
    {
        return $claims->map(function ($value, $name) {
            return $value instanceof ClaimInterface ? $value : $this->claimFactory->get($name, $value);
        });
    }
}
