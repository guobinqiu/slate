<?php

namespace Wenwen\FrontendBundle\Services;

use Predis\Client;
use JMS\Serializer\Serializer;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\ServiceDependency\HttpClient;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * 通过IP获得地域属性
 * Ref: http://lbs.amap.com/api/webservice/guide/api/ipconfig/#t2
 */
class IpLocationService
{
    private $redis;

    private $logger;

    private $em;

    private $parameterService;

    private $httpClient;

    // 这个service会访问外部的服务器
    // 开发和测试的过程中没有必要访问服务器
    // 在调用service的时候，通过setDummy(true/false)来控制是否访问外部的服务器
    private $dummy = false;
    private $dummyCityName = '上海市';
    private $dummyProvinceName = '上海市';

    public function __construct(
                                Client $redis,
                                LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                HttpClient $httpClient)
    {
        $this->redis = $redis;
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
        $this->httpClient = $httpClient;
    }

    public function setDummy($dummy, $cityName, $provinceName){
        $this->dummy = $dummy;
        $this->dummyCityName = $cityName;
        $this->dummyProvinceName = $provinceName;
    }

    /**
     * 通过IP地址获取城市和省份ID 只针对中国大陆地区
     * @param $ipAddress IP address (IPV4)
     * @return array('status', cityId', 'provinceId')
     */
    public function getLocationId($ipAddress) {
        $this->logger->debug(__METHOD__ . ' START ipAddress=' . $ipAddress);
        $locationId = array(
            'status' => false,
            'cityId' => 0,
            'provinceId' => 0
            );

        try{
            $cityName = $this->getCityName($ipAddress);

            if($cityName){
                $city = $this->em->getRepository('WenwenFrontendBundle:CityList')->findOneCityByNameLike($cityName);
                $this->logger->debug(__METHOD__ . ' city=' . json_encode($city));
                if($city){
                    $locationId['cityId'] = $city['cityId'];
                    $locationId['provinceId'] = $city['provinceId'];
                    $locationId['status'] = true;
                }
            }
            $this->logger->debug(__METHOD__ . ' locationId=' . json_encode($locationId));
        } catch(\Exception $e){
            $this->logger->error($e);
        }

        $this->logger->debug(__METHOD__ . ' END   ipAddress=' . $ipAddress . ' locationId=' . json_encode($locationId));
        return $locationId;
    }

    /**
     * 通过IP地址, 调用第三方API，获取城市名称 只针对中国大陆地区
     * @param $ipAddress
     * @return $cityName
     */
    private function getCityName($ipAddress) {
        $this->logger->debug(__METHOD__ . ' - START - ');
        $cityName = null;
        
        $responseBody = $this->getLocationJson($ipAddress);
        $this->logger->debug(__METHOD__ . ' responseBody=' . json_encode($responseBody));
        $rtn = $this->processResponseJson($responseBody);
        if($rtn['status']){
            $cityName = $rtn['city'];
        }

        $this->logger->debug(__METHOD__ . ' - END - ');
        return $cityName;
    }

    public function getLocationJson($ipAddress) {
        $this->logger->debug(__METHOD__ . ' START ');
        if($this->dummy){
            return $this->getDummyLocationJson();
        }

        if($ipAddress == '127.0.0.1'){
            return '';
        }

        $ipLocateApiKey = $this->parameterService->getParameter('amap.ip_locate_api.key');
        $params = array(
            'key' => $ipLocateApiKey,
            'ip' => $ipAddress,
            'output' => 'json'
            );
        $ipLocateApiUrl = $this->parameterService->getParameter('amap.ip_locate_api.url') . '?' . http_build_query($params);
        $this->logger->debug(__METHOD__ . ' ipLocateApiUrl=' . $ipLocateApiUrl);
        $request = $this->httpClient->get($ipLocateApiUrl, null, array('timeout' => 1, 'connect_timeout' => 1)); // 要快，不要精准
        $response = $request->send();
        if ($response->getStatusCode() != 200) {
            $this->logger->error($response->getStatusCode() . ' ' . $response->getBody());
            return '';
        }
        $this->logger->debug(__METHOD__ . ' response=' . $response);
        $responseBody = $response->getBody();
        $this->logger->debug(__METHOD__ . ' END responseBody=' . $responseBody);
        return $responseBody;
    }

    private function getDummyLocationJson() {
        $this->logger->debug(__METHOD__ . ' START ');
        $responseBody = 
        '{
            "status":"1",
            "info":"OK",
            "infocode":"10000",
            "province":"' . $this->dummyProvinceName . '",
            "city":"' . $this->dummyCityName . '",
            "adcode":"310000",
            "rectangle":"120.8397067,30.77980118;122.1137989,31.66889673"
        }';
        $this->logger->debug(__METHOD__ . ' END responseBody=' . json_encode($responseBody));
        return $responseBody;
    }


    /**
    * response的 status == 1 且，city为string类型时，返回city的内容
    * amap的这个API只提供对大陆的IP定位，非大陆IP的时候会返回一个city的空数组
    * @param $responseBody
    * @return array()
    */
    public function processResponseJson($responseBody){
        $rtn = array(
            'status' => false,
            'errmsg' => '',
            'city' => '没找到对应的城市',
            'province' => '没找到对应的省份'
            );
        
        $responseData = json_decode($responseBody, true);
        if($responseData['status'] == 1 && is_string($responseData['city'])){
            // 给的IP找不到对应城市时，返回的city是个空，json_decode以后就是个空数组
            $rtn['city'] = $responseData['city'];
            $rtn['province'] = $responseData['province'];
            $rtn['status'] = true;
        } else {
            $rtn['errmsg'] = $responseBody;
        }
        $this->logger->debug(__METHOD__ . ' END rtn=' . json_encode($rtn));
        return $rtn;
    }

    /**
     * 获取IP所对应的地区信息（省份名称，城市名称）
     * 先查找缓存，缓存中有的话，就不访问高德的API
     * 如果缓存没有，则访问高德的API，并记录该信息
     * @param $clientIp
     *
     */
    public function getLocationInfo($clientIp){
        $this->logger->debug(__METHOD__ . ' START clientIp=' . $clientIp);
        $val = $this->redis->get(CacheKeys::IP_LOCATION_PRE . $clientIp);
        if (is_null($val)) {
            $this->logger->debug(__METHOD__ . ' not found in redis. clientIp=' . $clientIp);
            $locationInfo = array(
                    'status' => false,
                    'errmsg' => '',
                    'city' => '没找到对应的城市',
                    'province' => '没找到对应的省份',
                    'clientIp' => $clientIp,
                    );

            // 缓存里没有该IP的位置信息，访问高德的API获取信息，并存在缓存里
            try {
                $responseBody = $this->getLocationJson($clientIp);
                $locationInfo = $this->processResponseJson($responseBody);
                $locationInfo['clientIp'] = $clientIp;
                if($locationInfo['status'] == true){
                    $this->logger->debug(__METHOD__ . ' got available info from apam.. ' . json_encode($locationInfo, true));
                    // 高德API有正常响应时，json化并记录在redis里，保留24小时
                    $this->redis->set(CacheKeys::IP_LOCATION_PRE . $clientIp, json_encode($locationInfo, true));
                    $this->redis->expire(CacheKeys::IP_LOCATION_PRE . $clientIp, CacheKeys::IP_LOCATION_TIMEOUT);
                } else {
                    $this->logger->debug(__METHOD__ . ' not available from apam. ' . json_encode($locationInfo, true));
                }
            } catch (\Exception $e){
                $this->logger->error(__METHOD__ . ' ' . $e->getMessage());
                $this->logger->error(__METHOD__ . ' ' . $e->getTraceAsString());
                $locationInfo['errmsg'] = $e->getMessage();
            }
        } else {
            $locationInfo = json_decode($val, true);
            $this->logger->debug(__METHOD__ . ' XX found in redis. locationInfo=' . json_encode($locationInfo, true));
        }
        return $locationInfo;
    }
}
