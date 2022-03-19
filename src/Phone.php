<?php
declare(strict_types=1);

namespace laocc\aliyun;

use AlibabaCloud\Dyplsapi\Dyplsapi;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;


class Phone extends _Base
{


    /**
     * @param string $poolID
     * @param string $mobile
     * @return false|mixed|string|string[]
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     *
     * {
     * "Message": "OK",
     * "RequestId": "0C30662C-5DB0-5E2C-8504-0199E27C948F",
     * "Code": "OK",
     * "SubsId": "1000054102517090,1000054102595464"
     * }
     */

    public function querySubID(string $poolID, string $mobile)
    {
        try {
            $request = Dyplsapi::v20170525()->querySubsId();
            $rest = $request
                ->withPoolKey($poolID)
                ->withPhoneNoX($mobile)
                ->debug($this->debug) // Enable the debug will output detailed information
                ->connectTimeout($this->timeout) // Throw an exception when Connection timeout
                ->timeout($this->timeout) // Throw an exception when timeout
                ->request()->toArray();

            if ($rest['Code'] !== 'OK') return $rest['Message'];

            return explode(',', $rest['SubsId']);

        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }

    /**
     * 解绑
     *
     * @param string $poolID 号池里
     * @param string $subID 绑定关系ID
     * @param string $subNO 隐私号码，主号，不是分机号
     * @return string|array
     */
    public function unbind(string $poolID, string $subID, string $subNO)
    {
        try {
            $request = Dyplsapi::v20170525()->unbindSubscription();
            return $request
                ->withPoolKey($poolID)
                ->withSubsId($subID)
                ->withSecretNo($subNO)
                ->debug($this->debug) // Enable the debug will output detailed information
                ->connectTimeout($this->timeout) // Throw an exception when Connection timeout
                ->timeout($this->timeout) // Throw an exception when timeout
                ->request()->toArray();
        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }


    /**
     * @param string $poolID
     * @param string $mobile
     * @param string $mobileB
     * @param int $expire
     * @param bool $recode
     * @return string|array
     */
    public function bind_AXN(string $poolID, string $mobile, string $mobileB, int $expire, bool $recode)
    {
        try {
            if (!$expire) $expire = time() + 365 * 86400;
            if ($expire < 750) $expire = 86400 * $expire;//小于750(2年)的，表示为天
            if ($expire < 315360000) $expire = time() + $expire;//若是小于10年的，则加上当前时间

            $expire = date('Y-m-d H:i:s', $expire);

            $this->debug([$poolID, $mobile, $mobileB, $expire, $recode]);

            $request = Dyplsapi::v20170525()->bindAxnExtension();
            return $request
                ->withPoolKey($poolID)
                ->withPhoneNoA($mobile)
                ->withPhoneNoB($mobileB)
//                ->withPhoneNoX('17092140924')
//                ->withExtension('123')
                ->withExpiration($expire)
                ->withIsRecordingEnabled($recode)
                ->debug($this->debug) // Enable the debug will output detailed information
                ->connectTimeout($this->timeout) // Throw an exception when Connection timeout
                ->timeout($this->timeout) // Throw an exception when timeout
                ->request()->toArray();

        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }


}