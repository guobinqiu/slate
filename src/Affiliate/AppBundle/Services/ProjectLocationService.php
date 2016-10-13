<?php

namespace Affiliate\AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\ServiceDependency\HttpClient;
use Wenwen\FrontendBundle\Services\ParameterService;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;
use Wenwen\FrontendBundle\Entity\ProvinceList;
use Wenwen\FrontendBundle\Entity\CityList;

/**
 * 通过IP获得地域属性
 * Ref: http://lbs.amap.com/api/webservice/guide/api/ipconfig/#t2
 */
class ProjectLocationService
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

    /**
     * 通过IP地址, 调用第三方API，获取城市名称 只针对中国大陆地区
     * @param $ipAddress
     * @return $cityName
     */
    private function getLocationName($ipAddress) {
        $this->logger->debug(__METHOD__ . ' - START - ');
        $locationName = array();
        $responseBody = $this->getLocationJson($ipAddress);
        $this->logger->debug(__METHOD__ . ' responseBody=' . json_encode($responseBody));
        $rtn = $this->processResponseJson($responseBody);
        if($rtn['status']){
            $locationName = array($rtn['province'], $rtn['city']);
        }
        $this->logger->debug(__METHOD__ . ' - END - ');
        return $locationName;
    }

    private function getLocationJson($ipAddress) {
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
    private function processResponseJson($responseBody){
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

    /**
    * 将多维数组变成一维数组
    * @param array
    * @return array
    */
    private function arrayChange($array){
        static $arrayTmp;
        foreach($array as $v)
        {
            if(is_array($v)){
                $this->arrayChange($v);
            } else {
                $arrayTmp[]=$v;
            }
        }
        return $arrayTmp;
    }

    /**
    * 匹配允许省份城市内的用户
    * @param $ipAddress, $affiliateProjectId
    * @return array entries
    */   
    public function confirmLocation($ipAddress, $affiliateProjectId){
        $projectLocationdb = $this->em->createQuery('SELECT p.province, p.city from AffiliateAppBundle:AffiliateProject p where p.id = :id')->setParameter('id', $affiliateProjectId)->getResult();
        
        $getClientLocation = $this->getLocationName($ipAddress);         
        $projectLocation = $this->arrayChange($projectLocationdb);
        foreach($getClientLocation as $clientLocation){
            $rtn = preg_grep("/$clientLocation/", $projectLocation);
        }
        return $rtn;
    }
    
    /**
    * 多城市多省份输入转换成数组
    * @param $city or $province
    * @return array
    */ 
    private function checkMultLocationInput($location){
        $tmpArray = explode(",", $location);
        if(count($tmpArray) > 1){
            return $locationArray = $tmpArray;
        } else {
            return $locationArray = $location;
        }
        
    }

    private function checkInputError($checkLocation){
        if($checkLocation !== null){
            $status = 'success';
            $msg = "Province/City check success";
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
            return $status;
        } else {
            $status = 'failure';
            $msg = " 输入省份/城市错误" . $checkLocation;
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
            return $status;
        }   
    }
  
    //检查输入的省份是否正确
    private function checkInputProvince($province){
        $provinceArray = $this->checkMultLocationInput($province);
        if(is_array($provinceArray)){
            $status = 'success';
            foreach ($provinceArray as $provinceKey){
                $checkProvince = $this->em->getRepository('Wenwen\FrontendBundle\Entity\ProvinceList')->findOneBy(array('provinceName'=>$provinceKey));
                if($checkProvince == null){
                    $status = 'failure';
                    $msg = " 输入省份错误";
                    $this->logger->error(__METHOD__ . $msg . PHP_EOL);
                    return $status;
                }
            }
            return $status;
        } else {
            $checkProvince = $this->em->getRepository('Wenwen\FrontendBundle\Entity\ProvinceList')->findOneBy(array('provinceName'=>$province));
            return $status = $this->checkInputError($checkProvince);
        }
    }   

    //检查输入的城市是否正确
    private function checkInputCity($city){
        $cityArray = $this->checkMultLocationInput($city);
        if(is_array($cityArray)){
            $status = 'success';
            foreach ($cityArray as $cityKey){
                $checkCity = $this->em->getRepository('Wenwen\FrontendBundle\Entity\CityList')->findOneBy(array('cityName'=>$cityKey));
                if($checkCity == null){
                    $status = 'failure';
                    $msg = " 输入城市错误";
                    $this->logger->error(__METHOD__ . $msg . PHP_EOL);
                    return $status;
                }
            }
            return $status;
        } else {
            $checkCity = $this->em->getRepository('Wenwen\FrontendBundle\Entity\CityList')->findOneBy(array('cityName'=>$city));
            return $status = $this->checkInputError($checkCity);
        }
    }

    /**
     * 检查输入的City和Province内容是否正确
     * @param $province, @city
     * @return $status string
     */ 
    public function checkInputLocation($province, $city){
        if(is_null($province)){
            if(is_null($city)){
                $status = 'success';
            } else {
                $status = $this->checkInputCity($city);                                                    
            }
        } else {
            $status = $this->checkInputProvince($province);
        }
        return $status;
    }    
}

