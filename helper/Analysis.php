<?php

namespace laocc\aliyun\helper;

use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use esp\core\Library;
use esp\error\Error;
use laocc\aliyun\Analysis as aliAnalysis;
use function esp\helper\is_domain;
use function esp\helper\is_ip;
use function esp\helper\is_url;

class Analysis extends Library
{
    private array $conf;

    public function _init(array $conf)
    {
        $this->conf = $conf;
    }


    /**
     * 检查域名解析是否合法
     *
     * @param string $type 类型
     * @param string $domain 解析域名
     * @param string $value 解析值
     * @return null|string
     */
    public function checkDomainAnalysis(string $type, string $domain, string $value)
    {
        $domPnt = '/^[a-z\d\-_]+(\.[a-z\d\-_]+)*$/i';
        $domTxt = '字母、数字、中横杠、下划线，以.号间隔，不得以.号开头';

        switch ($type = strtoupper($type)) {
            case 'A'://参考标准；RR值可为空，即@解析；不允许含有下划线；
            case 'AAAA'://参考标准；RR值可为空，即@解析；不允许含有下划线；
            case 'CAA'://参考标准；RR值可为空，即@解析；不允许含有下划线；
            case 'MX'://参考标准；RR值可为空，即@解析；不允许含有下划线 且不可为IP地址。1-10，优先级依次递减。
                if ($domain === '' or $domain === '*' or $domain === '@') goto chkValue1;
                else if (!preg_match('/^[a-z\d\-]+(\.[a-z\d\-]+)*$/i', $domain) and !preg_match('/^\*(\.[a-z\d\-]+)+$/i', $domain)) {
                    return "解析的域名不符合{$type}规则";
                }
                chkValue1:
                if ($type === 'MX' and (is_ip($value, 'ipv4') or is_ip($value, 'ipv6'))) return 'MX记录不可以为IP格式';
                else if ($type === 'A' and !is_ip($value, 'ipv4')) return 'A记录值须为IPv4格式';
                else if ($type === 'AAAA' and !is_ip($value, 'ipv6')) return 'AAAA记录值须为IPv6格式';
                break;

            case 'CNAME'://参考标准；另外，有效字符除字母、数字、“-”（中横杠）、还包括“_”(下划线)；RR值不允许为空（即@）；允许含有下划线
                if ($domain === '' or $domain === '@') goto chkValue2;
                else if ($domain === '*') goto chkValue2;
                else if (!preg_match($domPnt, $domain) and !preg_match('/^\*(\.[a-z\d\-_]+)+$/i', $domain)) {
                    return "CNAME解析的域名只允许：{$domTxt}，可以*开头的泛解析";
                }
                chkValue2:
                if (is_ip($value, 'ipv4') or is_ip($value, 'ipv6')) return 'CNAME解析目标不可以为IP4或IP6格式';
                else if (!is_domain($value)) return 'CNAME解析目标须是一个域名';
                break;

            case 'TXT'://参考标准；另外，有效字符除字母、数字、“-”（中横杠）、还包括“_”(下划线)；RR值可为空，即@解析；允许含有下划线；不支持泛解析
                if ($domain === '' or $domain === '@') return null;
                else if (strpos($domain, '*') !== false) return 'TXT不支持泛解析';
                else if (!preg_match($domPnt, $domain)) return "TXT解析的域名只允许：{$domTxt}";
                break;

            case 'NS'://参考标准；RR值不能为空；允许含有下划线；不支持泛解析
                if ($domain === '' or $domain === '@') return 'NS解析不能为空';
                else if (strpos($domain, '*') !== false) return 'NS不支持泛解析';
                else if (!preg_match($domPnt, $domain)) return "NS解析的域名只允许：{$domTxt}";
                break;

            case 'SRV'://是一个name，且可含有下划线“_“和点“.”；允许含有下划线；可为空（即@）；不支持泛解析
                if ($domain === '' or $domain === '@') return null;
                else if (strpos($domain, '*') !== false) return 'SRV不支持泛解析';
                else if (!preg_match($domPnt, $domain)) return "SRV解析的域名只允许：{$domTxt}";
                break;

            case 'REDIRECT_URL'://显性URL转发,参考标准；RR值可为空，即@解析
            case 'FORWARD_URL'://隐性URL转发,参考标准；RR值可为空，即@解析
                if ($domain === '' or $domain === '@') return '空头主机域名不支持URL转发';
                else if ($domain === '*') return '不支持泛域名URL转发';

                if (!is_url($value)) return 'URL转发目标须为URL格式';
                break;
            default:
                return null;
        }
        return null;
    }


    /**
     * @param array $param
     * @return array|string
     * @throws Error
     */
    public function add(array $param)
    {
        $ana = new aliAnalysis($this->conf);
        return $ana->insert($param);
    }

    /**
     * @throws Error
     */
    public function edit(array $param)
    {
        $ana = new aliAnalysis($this->conf);
        return $ana->update($param);
    }

    /**
     * @param array $param
     * @return array|string
     * @throws Error
     */
    public function delete(array $param)
    {
        $ana = new aliAnalysis($this->conf);
        try {
            return $ana->delete($param);
        } catch (ClientException|ServerException $e) {
        }
    }

    /**
     * @throws Error
     */
    public function remark(array $param)
    {
        $ana = new aliAnalysis($this->conf);
        return $ana->remarked($param);
    }


    /**
     * @param array $param
     * @return array
     * @throws Error
     */
    public function load(array $param): array
    {
        $ana = new aliAnalysis($this->conf);

        $analysis = $ana->download($param['domain']);
        $this->debug($analysis);
        $values = [];
        foreach ($analysis['DomainRecords']['Record'] as $an) {
            $value = [];
            $value['type'] = strtoupper($an['Type']);
            $value['domain'] = $an['RR'];
            $value['value'] = $an['Value'];
            $value['id'] = $an['RecordId'];
            $value['line'] = $an['Line'];
            $value['status'] = $an['Status'];
            $value['locked'] = $an['Locked'];
            $value['ttl'] = $an['TTL'];
            $value['remark'] = $an['Remark'] ?? '';
            $value['remark_url'] = urlencode($value['remark']);
            $value['weight'] = $an['Weight'] ?? 1;
            $value['mx'] = $an['Priority'] ?? 1;
            $values[] = $value;
        }

        return $values;
    }

}