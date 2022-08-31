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

use ArrayAccess;
use BadMethodCallException;
use Countable;
use FirecmsExt\Jwt\Claims\AbstractClaim;
use FirecmsExt\Jwt\Claims\Collection;
use FirecmsExt\Jwt\Contracts\PayloadValidatorInterface;
use FirecmsExt\Jwt\Exceptions\PayloadException;
use FirecmsExt\Jwt\Exceptions\TokenExpiredException;
use FirecmsExt\Jwt\Exceptions\TokenInvalidException;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Contracts\Jsonable;
use JsonSerializable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Payload implements ArrayAccess, Arrayable, Countable, Jsonable, JsonSerializable
{
    /**
     * The collection of claims.
     */
    private Collection $claims;

    private PayloadValidatorInterface $validator;

    /**
     * Build the Payload.
     */
    /**
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(Collection $claims, bool $ignoreExpired = false)
    {
        $this->validator = ApplicationContext::getContainer()->get(PayloadValidatorInterface::class);
        $this->claims = $this->validator->check($claims, $ignoreExpired);
    }

    /**
     * Get the payload as a string.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Invoke the Payload as a callable function.
     */
    public function __invoke(mixed $claim = null): mixed
    {
        return $this->get($claim);
    }

    /**
     * Magically get a claim value.
     *
     * @throws BadMethodCallException
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (preg_match('/get(.+)\b/i', $method, $matches)) {
            foreach ($this->claims as $claim) {
                if (get_class($claim) === 'FirecmsExt\\Jwt\\Claims\\' . $matches[1]) {
                    return $claim->getValue();
                }
            }
        }

        throw new BadMethodCallException(sprintf('The claim [%s] does not exist on the payload.', $method));
    }

    /**
     * Get the array of claim instances.
     */
    public function getClaims(): Collection
    {
        return $this->claims;
    }

    /**
     * Checks if a payload matches some expected values.
     */
    public function matches(array $values, bool $strict = false): bool
    {
        if (empty($values)) {
            return false;
        }

        $claims = $this->getClaims();

        foreach ($values as $key => $value) {
            if (! $claims->has($key) or ! $claims->get($key)->matches($value, $strict)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a payload strictly matches some expected values.
     */
    public function matchesStrict(array $values): bool
    {
        return $this->matches($values, true);
    }

    /**
     * Get the payload.
     */
    public function get(mixed $claim = null): mixed
    {
        $claim = value($claim);

        if ($claim !== null) {
            if (is_array($claim)) {
                return array_map([$this, 'get'], $claim);
            }

            return Arr::get($this->toArray(), $claim);
        }

        return $this->toArray();
    }

    /**
     * Get the underlying Claim instance.
     */
    public function getInternal(string $claim): AbstractClaim
    {
        return $this->claims->getByClaimName($claim);
    }

    /**
     * Determine whether the payload has the claim (by instance).
     */
    public function has(AbstractClaim $claim): bool
    {
        return $this->claims->has($claim->getName());
    }

    /**
     * Determine whether the payload has the claim (by key).
     */
    public function hasKey(string $claim): bool
    {
        return $this->offsetExists($claim);
    }

    /**
     * Get the array of claims.
     */
    public function toArray(): array
    {
        return $this->claims->toPlainArray();
    }

    /**
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the payload as JSON.
     */
    public function toJson(int $options = JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Determine if an item exists at an offset.
     */
    public function offsetExists(mixed $offset): bool
    {
        return Arr::has($this->toArray(), $offset);
    }

    /**
     * Get an item at a given offset.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return Arr::get($this->toArray(), $offset);
    }

    /**
     * Don't allow changing the payload as it should be immutable.
     *
     * @throws PayloadException
     */
    public function offsetSet(mixed $offset, mixed $value)
    {
        throw new PayloadException('The payload is immutable');
    }

    /**
     * Don't allow changing the payload as it should be immutable.
     *
     * @throws PayloadException
     */
    public function offsetUnset(mixed $offset)
    {
        throw new PayloadException('The payload is immutable');
    }

    /**
     * Count the number of claims.
     */
    public function count(): int
    {
        return count($this->toArray());
    }
}
