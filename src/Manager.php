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

use FirecmsExt\Jwt\Claims\Factory as ClaimFactory;
use FirecmsExt\Jwt\Contracts\CodecInterface;
use FirecmsExt\Jwt\Contracts\ManagerInterface;
use FirecmsExt\Jwt\Exceptions\JwtException;
use FirecmsExt\Jwt\Exceptions\TokenBlacklistedException;
use FirecmsExt\Jwt\Exceptions\TokenExpiredException;
use FirecmsExt\Jwt\Exceptions\TokenInvalidException;
use Hyperf\Utils\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Manager implements ManagerInterface
{
    /**
     * The JWT codec interface.
     */
    protected CodecInterface $codec;

    /**
     * The blacklist interface.
     */
    protected Blacklist $blacklist;

    /**
     * the claim factory.
     */
    protected ClaimFactory $claimFactory;

    /**
     * the payload factory.
     */
    protected PayloadFactory $payloadFactory;

    /**
     * The blacklist flag.
     */
    protected bool $blacklistEnabled = true;

    /**
     * the persistent claims.
     */
    protected array $persistentClaims = [];

    public function __construct(
        CodecInterface $codec,
        Blacklist $blacklist,
        ClaimFactory $claimFactory,
        PayloadFactory $payloadFactory
    ) {
        $this->codec = $codec;
        $this->blacklist = $blacklist;
        $this->claimFactory = $claimFactory;
        $this->payloadFactory = $payloadFactory;
    }

    /**
     * Encode a Payload and return the Token.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function encode(Payload $payload): Token
    {
        $token = $this->codec->encode($payload->get());

        return new Token($token);
    }

    /**
     * Decode a Token and return the Payload.
     *
     * @throws ContainerExceptionInterface
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     * @throws NotFoundExceptionInterface
     * @throws TokenBlacklistedException
     */
    public function decode(Token $token, bool $checkBlacklist = true, bool $ignoreExpired = false): Payload
    {
        $payload = $this->payloadFactory->make($this->codec->decode($token->get()), $ignoreExpired);

        if ($checkBlacklist and $this->blacklistEnabled and $this->blacklist->has($payload)) {
            throw new TokenBlacklistedException('The token has been blacklisted');
        }

        return $payload;
    }

    /**
     * Refresh a Token and return a new Token.
     *
     * @throws ContainerExceptionInterface
     * @throws JwtException
     * @throws NotFoundExceptionInterface
     * @throws TokenBlacklistedException
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     */
    public function refresh(Token $token, bool $forceForever = false, array $customClaims = []): Token
    {
        $claims = $this->buildRefreshClaims($this->decode($token, true, true));

        if ($this->blacklistEnabled) {
            // Invalidate old token
            $this->invalidate($token, $forceForever);
        }

        $claims = array_merge($claims, $customClaims);

        // Return the new token
        return $this->encode($this->payloadFactory->make($claims));
    }

    /**
     * Invalidate a Token by adding it to the blacklist.
     *
     * @throws ContainerExceptionInterface
     * @throws JwtException
     * @throws NotFoundExceptionInterface
     * @throws TokenBlacklistedException
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     */
    public function invalidate(Token $token, bool $forceForever = false): bool
    {
        if (! $this->blacklistEnabled) {
            throw new JwtException('You must have the blacklist enabled to invalidate a token.');
        }

        return call_user_func(
            [$this->blacklist, $forceForever ? 'addForever' : 'add'],
            $this->decode($token, false, true)
        );
    }

    /**
     * Get the Claim Factory instance.
     */
    public function getClaimFactory(): ClaimFactory
    {
        return $this->claimFactory;
    }

    /**
     * Get the Payload Factory instance.
     */
    public function getPayloadFactory(): PayloadFactory
    {
        return $this->payloadFactory;
    }

    /**
     * Get the JWT codec instance.
     */
    public function getCodec(): CodecInterface
    {
        return $this->codec;
    }

    /**
     * Get the Blacklist instance.
     */
    public function getBlacklist(): Blacklist
    {
        return $this->blacklist;
    }

    /**
     * Set whether the blacklist is enabled.
     *
     * @return $this
     */
    public function setBlacklistEnabled(bool $enabled): static
    {
        $this->blacklistEnabled = $enabled;

        return $this;
    }

    /**
     * Set the claims to be persisted when refreshing a token.
     *
     * @return $this
     */
    public function setPersistentClaims(array $claims): static
    {
        $this->persistentClaims = $claims;

        return $this;
    }

    /**
     * Get the claims to be persisted when refreshing a token.
     */
    public function getPersistentClaims(): array
    {
        return $this->persistentClaims;
    }

    /**
     * Build the claims to go into the refreshed token.
     */
    protected function buildRefreshClaims(Payload $payload): array
    {
        // Get the claims to be persisted from the payload
        $persistentClaims = Arr::only($payload->toArray(), $this->persistentClaims);

        // persist the relevant claims
        return array_merge(
            $persistentClaims,
            [
                'sub' => $payload['sub'],
                'iat' => $payload['iat'],
            ]
        );
    }
}
