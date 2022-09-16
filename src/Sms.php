<?php
declare(strict_types=1);

namespace laocc\aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Sms extends _Base
{

    /**
     * @param string $type
     * @param string $mobile
     * @param array $params
     * @return array|string
     */
    public function send(string $type, string $mobile, array $params = [])
    {
        if (!($params['code'] ?? '')) $params['code'] = $this->createSignCode($mobile);
        if (_DEBUG and !isset($params['test'])) return $params;
        if (is_string($this->conf['sign'])) $this->conf['sign'] = explode(',', $this->conf['sign']);

        $query = [
            'RegionId' => "cn-hangzhou",
            'PhoneNumbers' => $mobile,
            'SignName' => '',
            'TemplateCode' => $this->conf['template'][$type] ?? '',
            'TemplateParam' => json_encode($params, 256 | 64),
        ];
        if (empty($query['TemplateCode'])) return "未定义{$type}的模版ID";

        $signIndex = 0;
        $lastError = '';

        try {
            trySend:
            $query['SignName'] = $this->conf['sign'][$signIndex] ?? '';
            if (empty($query['SignName'])) return '没有可用签名';

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options(['query' => $query])
                ->request()
                ->toArray();

            if ($result['Code'] === 'OK') return $params + ['result' => $result];
            else if ($result['Code'] === 'isp.RAM_PERMISSION_DENY') return 'RAM账号权限不足';
            else if ($result['Code'] === 'isv.BUSINESS_LIMIT_CONTROL') {
                /**
                 * 解决方案： 请将短信发送频率限制在正常的业务流控范围内。
                 * 默认流控：使用同一个签名，对同一个手机号码发送短信验证码，支持1条/分钟，5条/小时 ，累计10条/天。
                 */
                $signIndex++;
                goto trySend;
            }
            return "发送失败2:{$result['Code']}，请稍后再试";

        } catch (ClientException | ServerException $e) {
            $lastError = $e->getErrorMessage();
            $signIndex++;
            goto trySend;
        }

    }

    /**
     * @param string $mobile
     * @param int $code
     * @return string|null
     */
    public function check(string $mobile, int $code): ?string
    {
        $sCode = $this->_controller->_redis->get("message_{$mobile}");
        if (!$sCode) return '验证码已过期，请重新发送验证码';
        if (intval($sCode) !== $code) return '验证码错误';
        $this->_controller->_redis->del("message_{$mobile}");
        return null;
    }

    /**
     * @param $mobile
     * @return int
     */
    public function delete($mobile): int
    {
        return $this->_controller->_redis->del("message_{$mobile}");
    }

    /**
     * @param string $mobile
     * @return string
     */
    private function createSignCode(string $mobile): string
    {
        $ttl = $this->conf['ttl'] ?? 10;
        $len = $this->conf['len'] ?? 4;
        if (!$ttl) $ttl = 10;
        if (!$len) $len = 4;
        $code = (string)mt_rand(intval('1' . str_repeat('0', $len - 1)), intval(str_repeat('9', $len)));
        if ($this->_controller->_redis) $this->_controller->_redis->set("message_{$mobile}", $code, $ttl * 60);
        return $code;
    }


}