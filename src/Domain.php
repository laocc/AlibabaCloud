<?php

namespace laocc\aliyun;

use AlibabaCloud\Alidns\Alidns;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use AlibabaCloud\Domain\Domain as DomainAli;


class Domain extends Base
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
     * 某域名解析明细
     *
     * @param string $domain
     * @return array|string
     */
    public function analysis(string $domain)
    {
        try {
            $request = Alidns::v20150109()->describeDomainRecords();
            return $request->withDomainName($domain)
                ->withPageSize($this->pageSize)
                ->withPageNumber($this->pageIndex)
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
     * 添加一条解析
     *
     * @param string $domain
     * @return array|string
     */
    public function add(string $domain)
    {
        try {
            $request = Alidns::v20150109()->describeDomainRecords();
            return $request->withDomainName($domain)
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