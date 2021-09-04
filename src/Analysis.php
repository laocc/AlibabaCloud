<?php

namespace laocc\aliyun;

use AlibabaCloud\Alidns\Alidns;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

/**
 * 域名解析
 * Class Analysis
 * @package laocc\aliyun
 */
class Analysis extends _Base
{
    public $type = ['A', 'NS', 'MX', 'TXT', 'CNAME', 'SRV', 'AAAA', 'CAA', 'REDIRECT_URL', 'FORWARD_URL'];

    /**
     * OpenAPI 名称    描述    操作
     * 添加解析             AddDomainRecord     * 根据传入参数添加解析记录    查看文档去调试
     * 查询域名所有解析     DescribeDomainRecords     * 根据传入参数获取指定主域名的所有解析记录列表    查看文档去调试
     * 解析日志 DescribeRecordLogs     * 根据传入参数获取域名的解析操作日志    查看文档去调试
     * 查某个具体解析，根据域名查 DescribeSubDomainRecords     * 根据传入参数获取某个固定子域名的所有解析记录列表    查看文档去调试
     * 查某个具体解析，根据RecordId查 DescribeDomainRecordInfo     * 获取解析记录的详细信息    查看文档去调试
     * 查 GetTxtRecordForVerify     * 生成txt记录。用于域名、子域名找回、添加子域名验证、批量找回等功能    查看文档去调试
     * 改 UpdateDomainRecord     * 根据传入参数修改解析记录    查看文档去调试
     * 改 UpdateDomainRecordRemark     * 修改解析记录的备注    查看文档去调试
     * 改 SetDomainRecordStatus     * 根据传入参数设置解析记录状态    查看文档去调试
     * 删，根据RecordId DeleteDomainRecord     * 根据传入参数删除解析记录    查看文档去调试
     * 删，根据子域名 DeleteSubDomainRecords     * 根据传入参数删除主机记录对应的解析记录    查看文档去调试
     */
    /**
     * 某域名解析明细
     *
     * @param string $domain
     * @return array|string
     */
    public function download(string $domain)
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
     * @param array $option
     * @return array|string
     */
    public function insert(array $option)
    {
        if (!in_array($option['type'], $this->type)) return "不支持的解析类型({$option['type']})";

        try {
            $request = Alidns::v20150109()->addDomainRecord();
            return $request->withDomainName($option['domain'])
                ->withRR($option['rr'])
                ->withType($option['type'])
                ->withValue($option['value'])
                ->withLine($option['line'])
                ->withTTL($option['ttl'])
                ->withPriority($option['mx'])
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
     * @param array $option
     * @return array|string
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     *
     */
    public function delete(array $option)
    {
        try {
            $request = Alidns::v20150109()->deleteDomainRecord();
            return $request->withRecordId($option['id'])
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
     * 更新解析
     *
     * @param array $option
     * @return array|string
     */
    public function update(array $option)
    {
        if (!in_array($option['type'], $this->type)) return "不支持的解析类型({$option['type']})";
        try {
            $request = Alidns::v20150109()->updateDomainRecord();
            return $request->withRecordId($option['id'])
                ->withRR($option['rr'])
                ->withType($option['type'])
                ->withValue($option['value'])
                ->withLine($option['line'])
                ->withTTL($option['ttl'])
                ->withPriority($option['mx'])
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

    public function remarked(array $option)
    {
        try {
            $request = Alidns::v20150109()->updateDomainRecordRemark();
            return $request->withRecordId($option['id'])
                ->withRemark($option['remark'])
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
     * 查询
     *
     * @param array $option
     * @return array|string
     */
    public function query(array $option)
    {
        try {
            $request = Alidns::v20150109()->addDomainRecord();
            return $request->withDomainName($option['domain'])
                ->withRR($option['rr'])
                ->withType($option['type'])
                ->withValue($option['value'])
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