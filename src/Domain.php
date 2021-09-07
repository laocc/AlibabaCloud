<?php

namespace laocc\aliyun;

use AlibabaCloud\Alidns\Alidns;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use AlibabaCloud\Domain\Domain as DomainAli;

/**
 * Class Domain
 * @package laocc\aliyun
 *
 * @method $this renewv($value)
 */
class Domain extends _Base
{
    /**
     * 账号中所有注册的域名
     *
     * @return array|string
     */
    public function all()
    {
        try {
            $request = DomainAli::v20180129()->queryDomainList();
            return $request
                ->withPageSize($this->pageSize)
                ->withPageNum($this->pageIndex)
                ->debug($this->debug)
                ->connectTimeout($this->timeout)
                ->timeout($this->timeout)
                ->request()
                ->toArray();

        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }


    /**
     * @param array $info
     * @return array|string
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     */
    public function renew(array $info)
    {
        try {
            $request = DomainAli::v20180129()->saveSingleTaskForCreatingOrderRenew();
            return $request
                ->withDomainName($info['domain'])
                ->withSubscriptionDuration($info['year'])
                ->withCurrentExpirationDate($info['expire'])
                ->debug($this->debug)
                ->connectTimeout($this->timeout)
                ->timeout($this->timeout)
                ->request()
                ->toArray();

        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }


}