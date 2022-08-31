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
namespace FirecmsExt\Jwt\RequestParser;

use FirecmsExt\Jwt\RequestParser\Handlers\AuthHeaders;
use FirecmsExt\Jwt\RequestParser\Handlers\Cookies;
use FirecmsExt\Jwt\RequestParser\Handlers\InputSource;
use FirecmsExt\Jwt\RequestParser\Handlers\QueryString;
use FirecmsExt\Jwt\RequestParser\Handlers\RouteParams;

class RequestParserFactory
{
    public function __invoke()
    {
        return make(RequestParser::class)->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
            new Cookies(),
        ]);
    }
}
