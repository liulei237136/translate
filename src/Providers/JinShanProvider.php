<?php

namespace MouYong\Translate\Providers;

use ArrayAccess;
use MouYong\Translate\Contracts\ProviderInterface;
use MouYong\Translate\Exceptions\TranslateException;
use MouYong\Translate\Translate;
use ZhenMu\Support\Traits\Clientable;
use ZhenMu\Support\Traits\DefaultClient;

class JinShanProvider extends AbstractProvider implements ProviderInterface, ArrayAccess
{
    use Clientable {
        Clientable::getOptions as getDefaultOptions;
    }
    use DefaultClient;

    const HTTP_URL = 'https://ifanyi.iciba.com/index.php';

    const HTTPS_URL = 'https://ifanyi.iciba.com/index.php';

    protected function getRequestParams($q, $from, $to)
    {
        return compact('from', 'to', 'q');
    }

    public function getOptions()
    {
        $options = array_merge($this->getDefaultOptions(), [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        return $options;
    }

    protected function getRequestQuery($q)
    {
        $data         = [
            'c'         => 'trans',
            'm'         => 'fy',
            'client'    => '6',
            'auth_user' => 'key_ciba',
        ];

        $data['sign'] = substr(bin2hex(md5(sprintf(
            "%s%sifanyicjbysdlove1%s",
            $data['client'],
            $data['auth_user'],
            $q
        ), true)), 0, 16);

        return $data;
    }

    public function translate(string $q, $from = 'auto', $to = 'auto')
    {
        $response = $this->post($this->getTranslateUrl(), [
            'query' => $this->getRequestQuery($q),
            'form_params' => $this->getRequestParams($q, $from, $to),
        ]);

        return new Translate($this->mapTranslateResult([
            'src' => $q,
            'dst' => $response['content']['out'],
            'original' => $response->toArray(),
        ]));
    }

    protected function mapTranslateResult(array $translateResult)
    {
        return [
            'src' => $translateResult['src'],
            'dst' => $translateResult['dst'],
            'original' => $translateResult['original'],
        ];
    }

    public function isErrorResponse(array $data): bool
    {
        return !empty($data['content']['error_code']);
    }

    public function handleErrorResponse(?string $content = null, array $data = [])
    {
        throw new TranslateException("请求接口错误，错误信息：{$data['content']['message']}", $data['content']['error_code']);
    }
}
