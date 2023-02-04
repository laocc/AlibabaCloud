<?php

namespace laocc\aliyun\helper;

use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use esp\core\Library;
use esp\error\Error;
use laocc\aliyun\Analysis;
use laocc\aliyun\Boss;
use laocc\aliyun\Cert;
use laocc\aliyun\Domain;
use laocc\aliyun\Rds;
use laocc\aliyun\Redis;
use laocc\aliyun\Service;
use function esp\helper\date_diffs;

class AliYunAsset extends Library
{
    private array $conf;

    public function _init(array $conf)
    {
        $this->conf = $conf;
    }

    public function itemInt(string $item): int
    {
        $value = [
            'domain' => 1,
            'cert' => 2,
            'ecs' => 4,
            'mysql' => 8,
            'redis' => 16,
            'balance' => 32,
        ];
        if ($item === 'all') return array_sum($value);
        return $value[$item] ?? 0;
    }


    /**
     * @throws ClientException
     * @throws ServerException
     * @throws Error
     */
    public function loadAliYunData(int $types = 0): array
    {
        $res = [];
        if ($types > 0 && ($types & 1)) $res['domain'] = $this->asyncDomain($this->conf);
        if ($types > 0 && ($types & 2)) $res['cert'] = $this->asyncCert($this->conf);
        if ($types > 0 && ($types & 4)) $res['ecs'] = $this->asyncService($this->conf);
        if ($types > 0 && ($types & 8)) $res['mysql'] = $this->asyncRds($this->conf);
        if ($types > 0 && ($types & 16)) $res['redis'] = $this->asyncRedis($this->conf);
        if ($types > 0 && ($types & 32)) $res['balance'] = $this->asyncBalance($this->conf);

        end:
        $time = time();
//        $this->debug($res);

        foreach ($res as $key => &$items) {
            switch ($key) {
                case 'redis':
                    foreach ($items as $i => &$redis) {
                        $redis['_expire'] = strtotime($redis['EndTime']);
                        $redis['EndTime'] = date('Y-m-d', $redis['_expire']);
                        $redis['_left'] = ($redis['_expire'] - $time);
                        $redis['_ttl'] = date_diffs($redis['_expire'], $time);
                    }
                    break;
                case 'mysql':
                case 'ecs':
                case 'domain':
                    foreach ($items as $i => &$redis) {
                        $redis['desc'] = ($redis['register']['Remark'] ?? '');
                        $redis['_expire'] = strtotime($redis['expire']);
                        $redis['expire'] = date('Y-m-d', $redis['_expire']);
                        $redis['_left'] = ($redis['_expire'] - $time);
                        $redis['_ttl'] = date_diffs($redis['_expire'], $time);
                    }
                    break;
                case 'cert':
                    foreach ($items as $i => &$redis) {
                        $redis['_expire'] = strtotime($redis['endDate']);
                        $redis['_left'] = ($redis['_expire'] - $time);
                        $redis['_ttl'] = date_diffs($redis['_expire'], $time);
                    }
                    break;
            }
        }

        return $res;
    }


    /**
     * @throws Error
     */
    private function asyncService(array $conf)
    {
        if (isset($conf['ecs'])) $conf = $conf['ecs'];
        $aliCert = new Service($conf);
        $RegionId = $conf['regionID'] ?? 'cn-shanghai';
        return $aliCert->load($RegionId);
    }


    /**
     * @throws Error
     */
    private function asyncRedis(array $conf)
    {
        if (isset($conf['redis'])) $conf = $conf['redis'];
        if (!isset($conf['regionID'])) $conf['regionID'] = 'cn-shanghai';
        $aliRds = new Redis($conf);
        return $aliRds->load();
    }

    /**
     * @throws ClientException
     * @throws ServerException
     * @throws Error
     */
    private function asyncRds(array $conf)
    {
        if (isset($conf['rds'])) $conf = $conf['rds'];
        if (!isset($conf['regionID'])) $conf['regionID'] = 'cn-shanghai';
        $aliRds = new Rds($conf);
        return $aliRds->load();
    }


    private function asyncBalance(array $conf)
    {
        if (isset($conf['balance'])) $conf = $conf['balance'];
        $boss = new Boss($conf);
        $resp = $boss->balance();
        if (empty($resp)) return '查询失败';
        return $resp;
    }

    /**
     * @throws Error
     */
    private function asyncCert(array $conf)
    {
        if (isset($conf['cert'])) $conf = $conf['cert'];
        $aliCert = new Cert($conf);
        $data = $aliCert->load();
        return $data['CertificateList'] ?? [];
    }


    /**
     * @throws ClientException
     * @throws ServerException
     * @throws Error
     */
    private function asyncDomain(array $conf)
    {
        if (isset($conf['domain'])) $conf = $conf['domain'];
        $aliAns = new Analysis($conf);
        $aliDomain = new Domain($conf);
        $resp = $aliDomain->all();
        $asDomain = [];
        foreach ($resp['Data']['Domain'] as $i => $domain) {
            $info = $aliDomain->read(['domain' => $domain['DomainName']]);
            $asy = $aliAns->download($domain['DomainName']);
            $asDomain[] = [
                'domain' => $domain['DomainName'],
                'expire' => $domain['ExpirationDate'],
                'org' => $info['ZhRegistrantOrganization'],
                'register' => $domain,
                'owner' => $info,
                'analysis' => $asy['DomainRecords']['Record'] ?? []
            ];
        }

        return $asDomain;
    }

}