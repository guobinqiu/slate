<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\ServiceDependency\HttpClient;

/**
 * 通过IP获得地域属性
 * Ref: http://lbs.amap.com/api/webservice/guide/api/ipconfig/#t2
 */
class IpLocationService
{
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

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                HttpClient $httpClient)
    {
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
     * @return array('cityId', 'provinceId')
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
                $city = $this->em->getRepository('JiliApiBundle:CityList')->findOneCityByNameLike($cityName);
                $this->logger->debug(__METHOD__ . ' city=' . json_encode($city));
                if($city){
                    $locationId['cityId'] = $city['cityId'];
                    $locationId['provinceId'] = $city['provinceId'];
                    $locationId['status'] = true;
                }
            }
            $this->logger->debug(__METHOD__ . ' locationId=' . json_encode($locationId));
        } catch(\Exception $e){
            $this->logger->error(__METHOD__ . $e);
        }

        $this->logger->debug(__METHOD__ . ' END   ipAddress=' . $ipAddress . ' locationId=' . json_encode($locationId));
        return $locationId;
    }

    /**
     * 通过IP地址, 调用第三方API，获取城市和省份名称 只针对中国大陆地区
     * @param $app_mid
     * @return $sop_api_url
     * @link https://console.partners.surveyon.com.dev.researchpanelasia.com/docs/v1_1/survey_list#json-api-integration
     */
    public function getCityName($ipAddress) {
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
        $this->logger->debug(__METHOD__ . ' response=' . $response);
        $responseBody = $response->getBody();
        $this->logger->debug(__METHOD__ . ' END responseBody=' . $responseBody);
        return $responseBody;
    }

    public function getDummyLocationJson() {
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
        return $rtn;
    }
}