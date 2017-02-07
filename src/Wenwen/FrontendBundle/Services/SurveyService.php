<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Predis\Client;
use Wenwen\FrontendBundle\Entity\User;
use Psr\Log\LoggerInterface;
use SOPx\Auth\V1_1\Util;
use Symfony\Component\Templating\EngineInterface;
use VendorIntegration\SSI\PC1\ProjectSurvey;
use VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;
use Wenwen\FrontendBundle\ServiceDependency\HttpClient;

/**
 * 访问外部服务器，获取用户可回答的问卷信息
 */
class SurveyService
{
    private $logger;
    private $em;
    private $parameterService;
    private $httpClient;
    private $templating;
    private $redis;
    private $surveyParnterService;
    private $surveySopService;
    private $surveyGmoService;

    // 这个service会访问外部的服务器
    // 开发和测试的过程中没有必要访问服务器
    // 在调用service的时候，通过setDummy(true/false)来控制是否访问外部的服务器
    private $dummy = false;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                HttpClient $httpClient,
                                EngineInterface $templating,
                                Client $redis,
                                SurveyPartnerService $surveyParnterService,
                                SurveySopService $surveySopService,
                                SurveyGmoService $surveyGmoService
    ) {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
        $this->httpClient = $httpClient;
        $this->templating = $templating;
        $this->redis = $redis;
        $this->surveyParnterService = $surveyParnterService;
        $this->surveySopService = $surveySopService;
        $this->surveyGmoService = $surveyGmoService;
    }

    public function setDummy($dummy){
        $this->dummy = $dummy;
    }

    /**
     * 尝试取得user_id对应的 APP_MID，如果没有的话就创建一个
     * @param $user_id 91wenwen的用户ID
     * @return $app_mid SOP的APP_MID
     */
    public function getSopRespondentId($user_id) {
        $this->logger->debug(__METHOD__ . ' - START - ');
        // 尝试取得user_id对应的 APP_MID，如果没有的话就创建一个 所以在这里不判断$sop_respondent是否存在
        $sop_respondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId($user_id);
        $app_mid = $sop_respondent->getId();
        $this->logger->debug(__METHOD__ . ' - END - sop_respondent_id=' . $app_mid);
        return $app_mid;
    }

    /**
     * 生成该用户用来访问SOP survey list的url
     * @param $app_mid
     * @return $sop_api_url
     * @link https://console.partners.surveyon.com.dev.researchpanelasia.com/docs/v1_1/survey_list#json-api-integration
     */
    private function buildSopSurveyListUrl($app_mid) {
        $this->logger->debug(__METHOD__ . ' - START - ');

        $sop_config = $this->parameterService->getParameter('sop');
        $app_id = $sop_config['auth']['app_id'];
        $host = $sop_config['host'];
        $app_secret = $sop_config['auth']['app_secret'];

        $sop_params = array (
            'app_id' => $app_id,
            'app_mid' => $app_mid,
            'time' => time()
        );
        $sop_params['sig'] = Util::createSignature($sop_params, $app_secret);

        $sop_api_url = 'https://'.$host.'/api/v1_1/surveys/json?'.http_build_query(array(
            'app_id' => $sop_params['app_id'],
            'app_mid' => $sop_params['app_mid'],
            'sig' => $sop_params['sig'],
            'time' => $sop_params['time'],
        ));

        $this->logger->debug(__METHOD__ . ' - END - ');
        return $sop_api_url;
    }

//        数据结构
//        {
//            meta: {code: 200 },
//            data: {
//                profiling: [...] 类型：属性问卷 回答完消失
//                user_agreement [0,1] 0: fulcrum, 1: cint 商业问卷 回答完消失
//                cint_research: [...] 　商业  有回答过标记
//                fulcrum_research: [...] 商业 回答完消失
//                research: [...]  商业问卷 有回答过标记
//            }
//        }
//        回答了profiling后research才会有数据
//        回答了user_agreement[fulcrum]后fulcrum_research才会有数据
//        回答了user_agreement[cint]后cint_research才有数据

    /**
     * @return json $dummy_res 模拟一个SOP survey list返回的数据
     */
    public function getDummySurveyListJson() {
        //构造一个仿真数据
        $dummy_res = '{ 
            "meta" : {
                "code": 200,
                "message": "" 
            },
             "data": {
                 "profiling": [
                     {
                         "url": "http://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?sig=2cec964cd9cd901d17725bd08131976a3ced393b160708fcce2d7767802023c5&next=%2Fprofile%2Fp%2Fq004&time=1438677550&app_id=25&sop_locale=&app_mid=18",
                         "name": "q004",
                         "title": "profiling"
                     }
                 ],
                 "research": [
                  {
                    "survey_id": "10000",
                    "quota_id": "20000",
                    "cpi": "1.23",
                    "ir": "80",
                    "loi": "10",
                    "is_answered": "0",
                    "is_closed": "0",
                    "title": "关于工作的调查（Not asnwered Not closed）",
                    "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                    "is_fixed_loi": "1",
                    "is_notifiable": "1",
                    "date": "2015-01-01",
                    "extra_info": {
                        "point": {
                             "screenout": "2",
                             "quotafull": "1",
                             "complete": "400"
                         }
                     }
                  },
                  {
                    "survey_id": "10001",
                    "quota_id": "20001",
                    "cpi": "1.00",
                    "ir": "50",
                    "loi": "20",
                    "is_answered": "1",
                    "is_closed": "0",
                    "title": "Example Research Survey (Asnwered Not Closed）",
                    "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                    "is_fixed_loi": "1",
                    "is_notifiable": "1",
                    "date": "2015-01-02",
                    "extra_info": { 
                        "point": {
                             "screenout": "2",
                             "quotafull": "1",
                             "complete": "300"
                         }
                     }
                  },
                  {
                    "survey_id": "10011",
                    "quota_id": "20001",
                    "cpi": "1.00",
                    "ir": "50",
                    "loi": "0",
                    "is_answered": "1",
                    "is_closed": "0",
                    "title": "Example Research Survey (Asnwered Not Closed）",
                    "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                    "is_fixed_loi": "0",
                    "is_notifiable": "1",
                    "date": "2015-01-02",
                    "extra_info": { 
                        "point": {
                             "screenout": "2",
                             "quotafull": "1",
                             "complete": "300"
                         }
                     }
                  },
                  {
                    "survey_id": "7436",
                    "quota_id": "20002",
                    "cpi": "2.34",
                    "ir": "90",
                    "loi": "10",
                    "is_answered": "0",
                    "is_closed": "0",
                    "title": "testtesttest",
                    "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                    "is_fixed_loi": "0",
                    "is_notifiable": "0",
                    "date": "2015-01-03",
                    "extra_info": { 
                        "point": {
                             "screenout": "30",
                             "quotafull": "30",
                             "complete": "400"
                         }
                     }
                  },
                  {
                    "survey_id": "10002",
                    "quota_id": "20002",
                    "cpi": "2.34",
                    "ir": "90",
                    "loi": "10",
                    "is_answered": "0",
                    "is_closed": "1",
                    "title": "Example Research Survey (Closed）",
                    "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                    "is_fixed_loi": "0",
                    "is_notifiable": "0",
                    "date": "2015-01-03",
                    "extra_info": { 
                        "point": {
                             "screenout": "30",
                             "quotafull": "30",
                             "complete": "600"
                         }
                     }
                  }
                ],
                 "user_agreement":[
                   {
                     "type": "Fulcrum",
                     "url": "http://researchpanelasia.com"
                   },
                   {
                     "type": "Cint",
                     "url": "http://www.d8aspring.com"
                   }
                 ],
                 "fulcrum_research":[
                   {
                     "survey_id": "4",
                     "quota_id": "10",
                     "cpi": "0.00",
                     "ir": "80",
                     "loi": "31",
                     "title": "Fulcrum Dummy Survey 4",
                     "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                     "date": "2015-01-01",
                     "extra_info": {
                         "point": {"complete": "300"}
                     }
                   },
                   {
                     "survey_id": "4",
                     "quota_id": "10",
                     "cpi": "0.00",
                     "ir": "80",
                     "loi": "20",
                     "title": "Fulcrum Dummy Survey 4",
                     "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                     "date": "2015-01-01",
                     "extra_info": {
                         "point": {"complete": "300"}
                     }
                   },
                   {
                     "survey_id": "3708",
                     "quota_id": "10",
                     "cpi": "0.00",
                     "ir": "80",
                     "loi": "10",
                     "title": "Fulcrum Dummy Survey 3708",
                     "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                     "date": "2015-01-01",
                     "extra_info": {
                         "point": {"complete": "500"}
                     }
                   }
                 ],
                 "cint_research": [
                     {
                       "survey_id": "10000",
                       "quota_id": "20000",
                       "cpi": "0.00",
                       "ir": "80",
                       "loi": "10",
                       "is_answered": "0",
                       "is_closed": "0",
                       "title": "Cint Dummy Survey",
                       "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                       "date": "2015-01-01",
                       "extra_info": {
                         "point": {
                           "complete": "400",
                           "screenout": "10",
                           "quotafull": "10"
                         }
                       }
                     },
                     {
                       "survey_id": "10002",
                       "quota_id": "20000",
                       "cpi": "0.00",
                       "ir": "80",
                       "loi": "10",
                       "is_answered": "1",
                       "is_closed": "0",
                       "title": "Cint Dummy Survey2",
                       "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
                       "date": "2015-01-01",
                       "extra_info": {
                         "point": {
                           "complete": "500",
                           "screenout": "10",
                           "quotafull": "10"
                         }
                       }
                     }
                  ]
               }
            }';

        return $dummy_res;
    }

    /**
     * @param $user_id
     * @return string json格式字符串
     */
    public function getSopSurveyListJson($user_id) {
        $this->logger->debug(__METHOD__ . ' - START - ');
        if($this->dummy){
            $this->logger->debug(__METHOD__ . ' - END - Dummy mode - ');
            return $this->getDummySurveyListJson();
        }

        // 取得app_mid
        $app_mid = $this->getSopRespondentId($user_id);

        // 生成sop_api_url
        $sop_api_url = $this->buildSopSurveyListUrl($app_mid);

        $request = $this->httpClient->get($sop_api_url, null, array('timeout' => 3, 'connect_timeout' => 3));
        $response = $request->send();
        if ($response->getStatusCode() != 200) {
            $this->logger->error('url=' . $sop_api_url . 'statusCode='. $response->getStatusCode() . ' body=' . $response->getBody());
            return '';
        }
        $this->logger->debug(__METHOD__ . ' - END - Real mode - ');
        return $response->getBody();
    }

    /**
     * ssi的数据设计的耦合性太大，测试时需要在数据库里准备很多关联数据
     * 这里返回一个假的ssi数据 方便本地测试以及单纯的页面改动
     * @return array $ssi_res
     */
    private function getDummySsiSurveyList() {
        $this->logger->debug(__METHOD__ . ' - START - Dummy mode - ');
        // 造一个假的ssi project survey数据
        // *这里需要注意* 2016/06/17
        // 由于这版修改的时候对于ssi的整体调用设计还不是很清楚
        // 这里准备的dummy数据仅仅保证的survey list中ssi的cover page正常显示
        // 但是对应的实际问卷的页面显示会不正常，需要在数据库中准备相应的数据才能正常显示
        // 理想中，假数据的准备不应该在这里做，应该在ssi的模块中做
        // 将来ssi的对接考虑重新做，到时候再整体调整
        $ssi_res = array ();
        $ssi_res['ssi_project_config'] = $this->parameterService->getParameter('ssi_project_survey');
        $ssi_res['needPrescreening'] = true;
        $item = [];
        $item['id'] = '555';
        $item['ssi_project_id'] = '1';
        $item['ssi_respondent_id'] = '4';
        $item['start_url_id'] = 'wiS0MTjBuaAI-yBaBgWj1RlxlIgMWFrQ';
        $item['answer_status'] = '0';
        $item['stash_data'] = '{"contactMethodId":74,"startUrlHead":"http:\/\/dkr1.ssisurveys.com\/projects\/boomerang?psid="}';
        $ssi_surveys = [];
        $ssi_surveys[] = new ProjectSurvey($item);

        $item['id'] = '555';
        $item['ssi_project_id'] = '2';
        $item['ssi_respondent_id'] = '4';
        $item['start_url_id'] = 'wiS0MTjBuaAI-yBaBgWj1RlxlIgMWFrQ';
        $item['answer_status'] = '0';
        $item['stash_data'] = '{"contactMethodId":74,"startUrlHead":"http:\/\/dkr1.ssisurveys.com\/projects\/boomerang?psid="}';
        $ssi_surveys[] = new ProjectSurvey($item);

        $item['id'] = '555';
        $item['ssi_project_id'] = '3';
        $item['ssi_respondent_id'] = '4';
        $item['start_url_id'] = 'wiS0MTjBuaAI-yBaBgWj1RlxlIgMWFrQ';
        $item['answer_status'] = '0';
        $item['stash_data'] = '{"contactMethodId":74,"startUrlHead":"http:\/\/dkr1.ssisurveys.com\/projects\/boomerang?psid="}';
        $ssi_surveys[] = new ProjectSurvey($item);

        $item['id'] = '555';
        $item['ssi_project_id'] = '4';
        $item['ssi_respondent_id'] = '4';
        $item['start_url_id'] = 'wiS0MTjBuaAI-yBaBgWj1RlxlIgMWFrQ';
        $item['answer_status'] = '0';
        $item['stash_data'] = '{"contactMethodId":74,"startUrlHead":"http:\/\/dkr1.ssisurveys.com\/projects\/boomerang?psid="}';
        $ssi_surveys[] = new ProjectSurvey($item);
        $ssi_res['ssi_surveys'] = $ssi_surveys;
        $this->logger->debug(__METHOD__ . ' - END - Dummy mode - ');
        return $ssi_res;
    }

    /**
     * 返回该用户的可回答问卷数据
     * @param string $user_id 用户id
     * @return array $ssi_res
     */
    private function getSsiSurveyList($user_id) {
        $this->logger->debug(__METHOD__ . ' - START - ');
        if($this->dummy){
            return $this->getDummySsiSurveyList();
        }

        $ssi_res = array ();
        $ssi_res['ssi_surveys'] = [];
        $ssi_res['ssi_project_config'] = $this->parameterService->getParameter('ssi_project_survey');
        // SSI respondent
        $ssi_respondent = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);

        if ($ssi_respondent) {
            // ssi_respondent信息不存在 根据用户的回答情况来决定用户是否需要回答prescreen
            $ssi_res['needPrescreening'] = $ssi_respondent->needPrescreening();
            if ($ssi_respondent->isActive()) {
                $dbh = $this->em->getConnection();
                $ssi_res['ssi_surveys'] = SsiProjectRespondentQuery::retrieveSurveysForRespondent($dbh, $ssi_respondent->getId());
            }
        } else {
            // ssi_respondent信息不存在，要求用户回答 prescreen
            $ssi_res['needPrescreening'] = true;
        }

        foreach($ssi_res['ssi_surveys'] as $key => $value){
            $this->logger->debug(__METHOD__ . ' ssi_surveys.ssiProjectId= ' . $value->getSsiProjectId());
        }

        $this->logger->debug(__METHOD__ . ' - END - ');
        return $ssi_res;
    }

    /**
     * 实际上是private函数，为了测试方便定义为public
     * @param $user
     * @param $locationInfo
     * @return array $partnerResearchs
     */
    public function getSurveyResearchArray($user, $locationInfo) {
        $this->logger->info(__METHOD__ . 'START userId=' . $user->getId());
        $partnerResearchs = array();
        try{
            $surveyPartners = $this->surveyParnterService->getSurveyPartnerListForUser($user, $locationInfo);
            foreach($surveyPartners as $surveyPartner){
                $this->logger->debug(__METHOD__ . ' ' . json_encode($surveyPartner));
                // 该用户符合回答这个问卷的条件
                $partnerResearch = array();
                $partnerResearch['surveyPartnerId'] = $surveyPartner->getId();
                $partnerResearch['surveyId'] = $surveyPartner->getSurveyId();
                $partnerResearch['title'] = $this->surveyParnterService->generateSurveyTitleWithSurveyId($surveyPartner);
                $partnerResearch['content'] = $surveyPartner->getContent();
                $partnerResearch['loi'] = $surveyPartner->getLoi();
                $partnerResearch['ir'] = $surveyPartner->getIr();
                $partnerResearch['url'] = $surveyPartner->getUrl();
                $partnerResearch['complete_point'] = $surveyPartner->getCompletePoint();
                $partnerResearch['is_answered'] = 0;
                $partnerResearchs[] = $partnerResearch;
            }
        } catch (\Exception $e){
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            $this->logger->info(__METHOD__ . 'ERROR userId=' . $user->getId());
            return array();
        }
        $this->logger->info(__METHOD__ . 'END userId=' . $user->getId());
        return $partnerResearchs;
    }

    /**
     * @param $user_id 用户id
     * @param $limit 返回的数据个数 0:全部返回
     * @param int $limit 0全部，>0截取到指定长度
     * @return array
     */
    public function getOrderedHtmlSurveyList($user_id, $locationInfo, $limit = 0) {
        // 这里不做逻辑判断，只通过组合数据来render页面数据，然后返回
        $html_survey_list = [];

        $sop = null;
        try {
            $result = $this->getSopSurveyListJson($user_id);
            $sop = json_decode($result, true);
            if ($sop['meta']['code'] != 200) {
                $this->logger->error('sopJson=' . $result);
                $sop = null;
            }
        }  catch(\Exception $e) {
            $this->logger->error($e);
            $sop = null;
        }

        $answerableSurveyCount = 0;

        if ($sop != null) {
            //SOP Profiling
            foreach ($sop['data']['profiling'] as $profiling) {
                //$profiling['url'] = $this->toProxyAddress($profiling['url']);
                $profiling = $this->surveySopService->addProfilingUrlToken($profiling, $user_id);
                // answerableSurveyCount : 没有可回答的商业问卷时，属性问卷里增加提示显示，告诉用户完成属性问卷会增加带来商业问卷的机会
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_profiling_item_template.html.twig', array('profiling' => $profiling, 'answerableSurveyCount' => $answerableSurveyCount));
                array_unshift($html_survey_list, $html);
            }

            foreach ($sop['data']['user_agreement'] as $user_agreement) {
                //Fulcrum Agreement
                if ($user_agreement['type'] == 'Fulcrum') {
                    $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/fulcrum_agreement_item_template.html.twig', array('fulcrum_user_agreement' => $user_agreement));
                    array_unshift($html_survey_list, $html);
                }
                //Cint Agreement
                if ($user_agreement['type'] == 'Cint') {
                    $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/cint_agreement_item_template.html.twig', array('cint_user_agreement' => $user_agreement));
                    array_unshift($html_survey_list, $html);
                }
            }
        }

        //SSI research survey
        try {
            $ssi_res = $this->getSsiSurveyList($user_id);
            if ($ssi_res['needPrescreening']) {
                // 需要用户去完成 prescreen
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/ssi_agreement_item_template.html.twig', $ssi_res);
                array_unshift($html_survey_list, $html);
            }
            if (!empty($ssi_res['ssi_surveys'])) {
                // 该用户有可回答的商业问卷，显示ssi的coverpage
                $ssi_res['count'] = sizeof($ssi_res['ssi_surveys']);
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/ssi_survey_cover_template.html.twig', $ssi_res);
                array_unshift($html_survey_list, $html);
            }
        } catch(\Exception $e) {
            $this->logger->error($e);
        }

        //Survey partner survey
        try {
            $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($user_id);
            if(is_null($user)){
                $this->logger->debug(__METHOD__ . ' user not found: id = ' . $user_id);
            } else {
                $partnerResearchs = $this->getSurveyResearchArray($user, $locationInfo);
                if (count($partnerResearchs) > 0) {
                    $this->logger->debug(__METHOD__ . ' partnerResearchs count = ' . count($partnerResearchs));
                    foreach ($partnerResearchs as $partnerResearch) {
                        // 该用户有可回答的商业问卷
                        $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/partner_research_item_template.html.twig',
                            array('partnerResearch' => $partnerResearch));
                        if ($partnerResearch['is_answered'] == 0) {
                            array_unshift($html_survey_list, $html);
                        } else {
                            array_push($html_survey_list, $html);
                        }
                    }
                }
                $this->logger->debug(__METHOD__ . ' partnerResearchs count = ' . count($partnerResearchs));
                $this->logger->debug(__METHOD__ . ' html_survey_list count = ' . count($html_survey_list));
            }
        } catch(\Exception $e) {
            $this->logger->error($e);
        }

        //GMO research survey
        try {
            $researches = $this->surveyGmoService->getSurveyList($user_id);
            foreach ($researches as $research) {
                $research['title'] = 'g' . $research['research_id'] . ' ' . $research['title'];
                if ($research['point_min'] < $research['point']) {
                    $research['point_range'] = $research['point_min'] . '-' . $research['point'];
                } else {
                    $research['point_range'] = $research['point'];
                }
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/gmo_research_item_template.html.twig', array('research' => $research));
                if ($research['is_closed'] == 0) {
                    if ($research['is_answered'] == 0) {
                        array_unshift($html_survey_list, $html);
                    } else {
                        array_push($html_survey_list, $html);
                    }
                }
            }
        } catch(\Exception $e) {
            $this->logger->error($e);
        }

        if ($sop != null) {
            //Fulcrum research survey
            foreach ($sop['data']['fulcrum_research'] as $fulcrum_research) {
                if (!$this->hasStopWord($fulcrum_research['url'])) {
                    $fulcrum_research['title'] = 'f' . $fulcrum_research['survey_id'] . ' ' . '商业调查问卷';
                    $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/fulcrum_research_item_template.html.twig', array('fulcrum_research' => $fulcrum_research));
                    array_unshift($html_survey_list, $html);
                    $answerableSurveyCount++;
                }
            }

            //Cint research survey
            foreach ($sop['data']['cint_research'] as $cint_research) {
                if ($cint_research['is_closed'] == 0) {
                    if (!$this->hasStopWord($cint_research['url'])) {
                        $cint_research['title'] = 'c' . $cint_research['survey_id'] . ' ' . '商业调查问卷';
                        $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/cint_research_item_template.html.twig', array('cint_research' => $cint_research));
                        if ($cint_research['is_answered'] == 0) {
                            array_unshift($html_survey_list, $html);
                            $answerableSurveyCount++;
                        } else {
                            array_push($html_survey_list, $html);
                        }
                    }
                }
            }

            //SOP research survey
            foreach ($sop['data']['research'] as $research) {
                if ($research['is_closed'] == 0) {
                    if (!$this->hasStopWord($research['url'])) {
                        $research['title'] = 'r' . $research['survey_id'] . ' ' . $research['title'];
                        $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_research_item_template.html.twig', array('research' => $research));
                        if ($research['is_answered'] == 0) {
                            array_unshift($html_survey_list, $html);
                            $answerableSurveyCount++;
                        } else {
                            array_push($html_survey_list, $html);
                        }
                    }
                }
            }
        }

        return $limit > 0 ? array_slice($html_survey_list, 0, $limit) : $html_survey_list;
    }

    /**
     * 获取指定用户可回答的属性问卷的信息
     * 获取失败的时候返回一个空array
     * @param  string $user_id
     * @return array $sop_profiling_info
     *               $sop_profiling_info['profiling']['url']   属性问卷的URL
     *               $sop_profiling_info['profiling']['name']  属性问卷的问题编号
     *               $sop_profiling_info['profiling']['title'] 属性问卷的问题标题
     */
    public function getSopProfilingSurveyInfo($user_id) {
        $sop_profiling_info = [];

        // 获取sop的数据
        $result = $this->getSopSurveyListJson($user_id);
        $sop = json_decode($result, true);

        // 处理sop的数据
        if ($sop['meta']['code'] != 200) {
            $this->logger->error($result);
        } else {
            $this->logger->info($result);
            $sop_profiling_info['profiling'] = $sop['data']['profiling'][0];
        }
        $this->logger->debug(__METHOD__ . ' - END - ');
        return $sop_profiling_info;
    }

    public function pushBasicProfile(User $user)
    {
        try {
            $sop_config = $this->parameterService->getParameter('sop');
            $app_id = $sop_config['auth']['app_id'];
            $app_secret = $sop_config['auth']['app_secret'];
            $host = $sop_config['console_host'];

            $app_mid = $this->getSopRespondentId($user->getId());
            $userProfile = $this->em->getRepository('WenwenFrontendBundle:UserProfile')->findOneBy(array('user' => $user));

            $data = array(
                'app_id' => $app_id,
                'app_mid' => $app_mid,
                'time' => time(),
                'profile' => array(
                    'q001' => $userProfile->getBirthday(),
                    'q002' => $userProfile->getSex(),
                    'q004' => $userProfile->getCity(),
                )
            );

            $postBody = json_encode($data, true);
            //echo $postBody;

            $sig = Util::createSignature($postBody, $app_secret);
            //echo $sig;

            $headers = array(
                'Content-Type' => 'application/json',
                'X-Sop-Sig' => $sig
            );

            $url = 'http://' . $host . '/api/v1_1/resource/app/member';
            $request = $this->httpClient->post($url, $headers, $postBody, array('timeout' => 30, 'connect_timeout' => 30));
            $response = $request->send();
            $this->logger->info(__METHOD__ . $response->getBody());

        } catch(\Exception $e) {
            $this->logger->error(__METHOD__ . $e->getMessage());
            $this->logger->info(__METHOD__ . ' postBody=' . $postBody);
            $this->logger->info(__METHOD__ . ' sig=' . $sig);

            //throw $e;
            return false;
        }

        //return $response->getBody();
        return true;
    }

    /**
     * 屏蔽带有注册URL的问卷
     *
     * @param $url
     * @return int
     */
    private function hasStopWord($url) {
        $patten = "/(sign(.?)up|register|registeration)/i";
        return preg_match($patten, $url);
    }
    
    /**
     * 替换属性问卷中的SOP地址为PROXY地址
     *
     * @param $url
     * @return int
     */
    private function toProxyAddress($url) {
        return preg_replace('/surveyon.com/', 'surveyon.cn', $url);
    }
}
