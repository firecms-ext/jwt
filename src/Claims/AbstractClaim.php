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
namespace FirecmsExt\Jwt\Claims;

use FirecmsExt\Jwt\Contracts\ClaimInterface;
use FirecmsExt\Jwt\Contracts\ManagerInterface;
use Hyperf\Contract\Arrayable;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Contracts\Jsonable;
use JsonSerializable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractClaim implements ClaimInterface, Arrayable, Jsonable, JsonSerializable
{
    /**
     * The claim name.
     */
    protected string $name;

    /**
     * The claim value.
     */
    private mixed $value;

    private Factory $factory;

    public function __construct(mixed $value)
    {
        $this->setValue($value);
    }

    /**
     * Get the payload as a string.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Set the claim value, and call a validate method.
     *
     * @return $this
     */
    public function setValue(mixed $value): static
    {
        $this->value = $this->validateCreate($value);

        return $this;
    }

    /**
     * Get the claim value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Set the claim name.
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the claim name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Validate the claim in a standalone Claim context.
     */
    public function validateCreate(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Checks if the value matches the claim.
     */
    public function matches(mixed $value, bool $strict = true): bool
    {
        return $strict ? $this->value === $value : $this->value == $value;
    }

    /**
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Build a key value array comprising the claim name and value.
     */
    public function toArray(): array
    {
        return [$this->getName() => $this->getValue()];
    }

    /**
     * Get the claim as JSON.
     */
    public function toJson(int $options = JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getFactory(): Factory
    {
        if (! empty($this->factory)) {
            return $this->factory;
        }
        return $this->factory = ApplicationContext::getContainer()->get(ManagerInterface::class)->getClaimFactory();
    }
}
