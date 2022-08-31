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
namespace FirecmsExt\Jwt;

use FirecmsExt\Jwt\Contracts\StorageInterface;

class Blacklist
{
    /**
     * The storage.
     */
    protected StorageInterface $storage;

    /**
     * The grace period when a token is blacklisted. In seconds.
     */
    protected int $gracePeriod;

    /**
     * Number of seconds from issue date in which a JWT can be refreshed.
     */
    protected ?int $refreshTtl;

    /**
     * The unique key held within the blacklist.
     */
    protected string $key = 'jti';

    /**
     * Constructor.
     */
    public function __construct(StorageInterface $storage, int $gracePeriod, ?int $refreshTtl)
    {
        $this->storage = $storage;
        $this->gracePeriod = $gracePeriod;
        $this->refreshTtl = $refreshTtl;
    }

    /**
     * Add the token (jti claim) to the blacklist.
     */
    public function add(Payload $payload): bool
    {
        // if there is no exp claim then add the jwt to
        // the blacklist indefinitely
        if (! $payload->hasKey('exp')) {
            return $this->addForever($payload);
        }

        // if we have already added this token to the blacklist
        if (! empty($this->storage->get($this->getKey($payload)))) {
            return true;
        }

        $this->storage->add(
            $this->getKey($payload),
            ['valid_until' => $this->getGraceTimestamp()],
            $this->getSecondsUntilExpired($payload)
        );

        return true;
    }

    /**
     * Add the token (jti claim) to the blacklist indefinitely.
     */
    public function addForever(Payload $payload): bool
    {
        $this->storage->forever($this->getKey($payload), 'forever');

        return true;
    }

    /**
     * Determine whether the token has been blacklisted.
     */
    public function has(Payload $payload): bool
    {
        $val = $this->storage->get((string) $this->getKey($payload));

        // exit early if the token was blacklisted forever,
        if ($val === 'forever') {
            return true;
        }

        // check whether the expiry + grace has past
        return ! empty($val) and ! Utils::isFuture($val['valid_until']);
    }

    /**
     * Remove the token (jti claim) from the blacklist.
     */
    public function remove(Payload $payload): bool
    {
        return $this->storage->destroy($this->getKey($payload));
    }

    /**
     * Remove all tokens from the blacklist.
     */
    public function clear(): bool
    {
        $this->storage->flush();

        return true;
    }

    /**
     * Set the grace period.
     *
     * @return $this
     */
    public function setGracePeriod(int $gracePeriod): static
    {
        $this->gracePeriod = (int) $gracePeriod;

        return $this;
    }

    /**
     * Get the grace period.
     */
    public function getGracePeriod(): int
    {
        return $this->gracePeriod;
    }

    /**
     * Get the unique key held within the blacklist.
     */
    public function getKey(Payload $payload): mixed
    {
        return $payload($this->key);
    }

    /**
     * Set the unique key held within the blacklist.
     *
     * @return $this
     */
    public function setKey(string $key): static
    {
        $this->key = value($key);

        return $this;
    }

    /**
     * Set the refresh time limit.
     *
     * @return $this
     */
    public function setRefreshTtl(?int $refreshTtl): static
    {
        $this->refreshTtl = $refreshTtl === null ? null : (int) $refreshTtl;

        return $this;
    }

    /**
     * Get the refresh time limit.
     */
    public function getRefreshTtl(): ?int
    {
        return $this->refreshTtl;
    }

    /**
     * Get the number of seconds until the token expiry.
     */
    protected function getSecondsUntilExpired(Payload $payload): int
    {
        $exp = Utils::timestamp($payload['exp']);
        $iat = Utils::timestamp($payload['iat']);

        // get the latter of the two expiration dates and find
        // the number of seconds until the expiration date,
        // plus 1 minute to avoid overlap
        return $exp->max($iat->addSeconds($this->refreshTtl))->addMinute()->diffInRealSeconds();
    }

    /**
     * Get the timestamp when the blacklist comes into effect
     * This defaults to immediate (0 seconds).
     */
    protected function getGraceTimestamp(): int
    {
        return Utils::now()->addSeconds($this->gracePeriod)->getTimestamp();
    }
}
