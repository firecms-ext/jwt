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

use FirecmsExt\Jwt\Contracts\JwtFactoryInterface;
use Hyperf\Contract\ConfigInterface;

class JwtFactory implements JwtFactoryInterface
{
    protected bool $lockSubject = true;

    public function __construct(ConfigInterface $config)
    {
        $this->lockSubject = (bool) $config->get('jwt.lock_subject');
    }

    public function make(): Jwt
    {
        return make(Jwt::class)->setLockSubject($this->lockSubject);
    }
}
