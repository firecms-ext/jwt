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

use FirecmsExt\Jwt\Commands\GenJwtKeypairCommand;
use FirecmsExt\Jwt\Commands\GenJwtSecretCommand;
use FirecmsExt\Jwt\Contracts\JwtFactoryInterface;
use FirecmsExt\Jwt\Contracts\ManagerInterface;
use FirecmsExt\Jwt\Contracts\PayloadValidatorInterface;
use FirecmsExt\Jwt\Contracts\RequestParser\RequestParserInterface;
use FirecmsExt\Jwt\Contracts\TokenValidatorInterface;
use FirecmsExt\Jwt\RequestParser\RequestParserFactory;
use FirecmsExt\Jwt\Validators\PayloadValidator;
use FirecmsExt\Jwt\Validators\TokenValidator;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ManagerInterface::class => ManagerFactory::class,
                TokenValidatorInterface::class => TokenValidator::class,
                PayloadValidatorInterface::class => PayloadValidator::class,
                RequestParserInterface::class => RequestParserFactory::class,
                JwtFactoryInterface::class => JwtFactory::class,
            ],
            'commands' => [
                GenJwtSecretCommand::class,
                GenJwtKeypairCommand::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for firecms-ext/jwt.',
                    'source' => __DIR__ . '/../publish/jwt.php',
                    'destination' => BASE_PATH . '/config/autoload/jwt.php',
                ],
            ],
        ];
    }
}
