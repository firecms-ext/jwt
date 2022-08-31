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
namespace FirecmsExt\Jwt\RequestParser\Handlers;

use FirecmsExt\Jwt\Contracts\RequestParser\HandlerInterface as ParserContract;
use Psr\Http\Message\ServerRequestInterface;

class AuthHeaders implements ParserContract
{
    /**
     * The header name.
     */
    protected string $header = 'authorization';

    /**
     * The header prefix.
     */
    protected string $prefix = 'bearer';

    public function parse(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine($this->header);

        if ($header and preg_match('/' . $this->prefix . '\s*(\S+)\b/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Set the header name.
     *
     * @return $this
     */
    public function setHeaderName(string $headerName): static
    {
        $this->header = $headerName;

        return $this;
    }

    /**
     * Set the header prefix.
     *
     * @return $this
     */
    public function setHeaderPrefix(string $headerPrefix): static
    {
        $this->prefix = $headerPrefix;

        return $this;
    }
}
