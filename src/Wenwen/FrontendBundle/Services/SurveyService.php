<?php

namespace Wenwen\FrontendBundle\Services;

use Guzzle\Http\Exception\CurlException;

class SurveyService extends BaseService
{
    private $http_client;

    private $templating;

    public function __construct($http_client, $templating)
    {
        $this->http_client = $http_client;
        $this->templating = $templating;
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
     * @param $sop_api_url
     * @return string json
     * @throws 抛网络连接异常
     */
    public function getSurveyListJson($sop_api_url) {
        try {
            $request = $this->http_client->get($sop_api_url);
            $response = $request->send();
            return $this->extractRealpart($response->getBody());
        } catch(CurlException $e) {
            throw $e;
        }

//          构造一个仿真数据
//          $dummy_res = '{ "meta" : {"code": "200" },
//             "data": {
//                 "profiling": [
//                     {
//                         "url": "https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?sig=2cec964cd9cd901d17725bd08131976a3ced393b160708fcce2d7767802023c5&next=%2Fprofile%2Fp%2Fq004&time=1438677550&app_id=25&sop_locale=&app_mid=13",
//                         "name": "q004",
//                         "title": "profiling"
//                     }
//                 ],
//                 "research": [
//                     {
//                         "date": "2015-07-21",
//                         "is_answered": "0",
//                         "cpi": "0.00",
//                         "is_closed": "0",
//                         "ir": "0",
//                         "extra_info": {
//                             "point": {
//                                 "screenout": "30",
//                                 "quotafull": "30",
//                                 "complete": "670"
//                             },
//                             "date": {
//                                 "end_at": "2015-08-31 00:00:00",
//                                 "start_at": "2015-07-21 00:00:00"
//                             },
//                             "content": ""
//                         },
//                         "url": "https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?sig=aaeca59caa406fff786976df7300ddc69992f75ffdbb4ea0616a868cf58062e5&next=%2Fproject_survey%2F393&time=1438677550&app_id=25&sop_locale=&app_mid=13",
//                         "loi": "15",
//                         "title": "Test 4",
//                         "survey_id": "284",
//                         "quota_id": "393"
//                     }
//                 ],
//                 "user_agreement":[
//                   {
//                     "type": "Fulcrum",
//                     "url": "http://researchpanelasia.com"
//                   },
//                   {
//                     "type": "Cint",
//                     "url": "http://www.d8aspring.com"
//                   }
//                 ],
//                 "fulcrum_research":[
//                   {
//                     "survey_id": "4",
//                     "quota_id": "10",
//                     "cpi": "0.00",
//                     "ir": "80",
//                     "loi": "10",
//                     "title": "来自Fulcrum的调查问卷",
//                     "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
//                     "date": "2015-01-01",
//                     "extra_info": {
//                         "point": {"complete": "10"}
//                     }
//                   }
//                 ],
//                 "cint_research": [
//                     {
//                       "survey_id": "10000",
//                       "quota_id": "20000",
//                       "cpi": "0.00",
//                       "ir": "80",
//                       "loi": "10",
//                       "is_answered": "0",
//                       "is_closed": "0",
//                       "title": "Cint Survey",
//                       "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
//                       "date": "2015-01-01",
//                       "extra_info": {
//                         "point": {
//                           "complete": "40",
//                           "screenout": "10",
//                           "quotafull": "10"
//                         }
//                       }
//                     },
//                     {
//                       "survey_id": "10002",
//                       "quota_id": "20000",
//                       "cpi": "0.00",
//                       "ir": "80",
//                       "loi": "10",
//                       "is_answered": "1",
//                       "is_closed": "0",
//                       "title": "Cint Survey2",
//                       "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
//                       "date": "2015-01-01",
//                       "extra_info": {
//                         "point": {
//                           "complete": "40",
//                           "screenout": "10",
//                           "quotafull": "10"
//                         }
//                       }
//                     }
//                  ]
//               }
//            }';
//
//        return $dummy_res;
    }

    /**
     * @param $arr 一堆乱七八糟的参数，先全部仍进来再按需拿取吧
     * @param int $limit 0全部，>0截取到指定长度
     * @return array
     */
    public function getOrderedHtmlServeyList($arr, $limit = 0) {
        $html_survey_list = [];

        //处理ssi ssi要放在列表的最下面所以代码要放在最上面执行
        $ssi_respondent = $arr['ssi_respondent'];
        if ($ssi_respondent == null || $ssi_respondent->needPrescreening()) {
            $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/ssi_user_agreement_item_template.html.twig', $arr);
            array_unshift($html_survey_list, $html);
        } elseif ($ssi_respondent->isActive() && $arr['ssi_surveys']) {
            $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/ssi_survey_cover_template.html.twig', $arr);
            array_unshift($html_survey_list, $html);
        }

        //处理sop
        $sop = json_decode($this->getSurveyListJson($arr['sop_api_url']), true);

        if ($sop['meta']['code'] != 200) {
            $this->logger->error($sop['meta']['message']);
            return $html_survey_list;
        }

        $fulcrum_researches = $sop['data']['fulcrum_research'];
        if (count($fulcrum_researches) > 0) {
            foreach ($fulcrum_researches as $fulcrum_research) {
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_fulcrum_research_item_template.html.twig', $fulcrum_research);
                array_unshift($html_survey_list, $html);
            }
        }

        $cint_researches = $sop['data']['cint_research'];
        if (count($cint_researches) > 0) {
            foreach ($cint_researches as $cint_research) {
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_cint_research_item_template.html.twig', $cint_research);
                if ($cint_research['is_answered'] == 0) {
                    array_unshift($html_survey_list, $html);
                } else {
                    array_push($html_survey_list, $html);
                }
            }
        }

        $researches = $sop['data']['research'];
        if (count($researches) > 0) {
            foreach ($researches as $research) {
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_research_item_template.html.twig', $research);
                if ($research['is_answered'] == 0) {
                    array_unshift($html_survey_list, $html);
                } else {
                    array_push($html_survey_list, $html);
                }
            }
        }

        $user_agreements = $sop['data']['user_agreement'];
        if (count($user_agreements) > 0 ) {
            foreach ($user_agreements as $user_agreement) {
                if ($user_agreement['type'] == 'Fulcrum') {
                    $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_fulcrum_user_agreement_item_template.html.twig', $user_agreement);
                    array_unshift($html_survey_list, $html);
                }
                if ($user_agreement['type'] == 'Cint') {
                    $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_cint_user_agreement_item_template.html.twig', $user_agreement);
                    array_unshift($html_survey_list, $html);
                }
            }
        }

        $profilings = $sop['data']['profiling'];
        if (count($profilings) > 0) {
            foreach ($profilings as $profiling) {
                $html = $this->templating->render('WenwenFrontendBundle:Survey:templates/sop_profiling_item_template.html.twig', $profiling);
                array_unshift($html_survey_list, $html);
            }
        }

        return $limit > 0 ? array_slice($html_survey_list, 0, $limit) : $html_survey_list;
    }

    private function extractRealpart($content) {
        $remove_head = "surveylistCallback(";
        $remove_tail = ");";
        $content = substr($content, strlen($remove_head));
        $content = substr($content, 0, 0 - strlen($remove_tail));
        return $content;
    }
}