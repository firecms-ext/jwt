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
namespace FirecmsExt\Jwt\RequestParser\Handlers;

use FirecmsExt\Jwt\Contracts\RequestParser\HandlerInterface as ParserContract;
use Psr\Http\Message\ServerRequestInterface;

class QueryString implements ParserContract
{
    use KeyTrait;

    public function parse(ServerRequestInterface $request): ?string
    {
        $data = data_get($request->getQueryParams(), $this->key);
        return empty($data) === null ? null : (string) $data;
    }
}
