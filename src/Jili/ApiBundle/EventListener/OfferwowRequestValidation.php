<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\OfferwowOrder,
    Jili\ApiBundle\Component\OrderBase;


/**
 * 
 **/
class OfferWowRequestValidation
{
    private $logger;
    private $em;
    public function __construct(LoggerInterface $logger, EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     *
     * @return void
     **/
    public function validate(Request $request, array $config)
    {

        $ret  = array( 0=> true, 'message'=> 'validated','code'=>'', 'data'=> array());

        $validations_config = $config['validations'];
//        memberid  true  您的网站用户的唯一编号，与“步骤3”memberid的对应
//        point true 奖励用户的虚拟货币数量
//        eventid true 回传数据的唯一流水号，合作客户需要记录并且验证唯一性，主要用于结算和对账
//        websiteid true 网站ID 
//        immediate true 0：非即时返利活动,处于待审核状态；
        //0.
        
            //1.
            foreach( array( 'memberid', 'point', 'eventid', 'websiteid', 'immediate') as $field) {
                if( '' ===  (string) $request->query->get($field, '') ) {
                    $ret [0] = false;
                    $ret['message'] = $validations_config [1] [ 'message'];
                    $ret['code'] = $validations_config[1] ['errorno'];
                    return $ret;
                }
            }

        // sign auth:  sign=md5(memberId+point+eventId+websiteId+immediate+key)
        if( '' !==  (string) $request->query->get('sign', '') ) {

            $hash = array( 
                $request->query->get('memberid'), 
                $request->query->get('point'), 
                $request->query->get('eventid'), 
                $request->query->get('websiteid'), 
                $request->query->get('immediate'), 
                $config['key']);

            if( strtoupper(md5(implode($hash) )) !==  $request->query->get('sign') ) {
                $ret [0] = false;
                $ret['message'] = $validations_config ['sign'] [ 'message'];
                $ret['code'] = $validations_config['sign'] ['errorno'];
                return $ret;
            } else {
            }
        }

        //2 offerwow-02 网站id不存在
        $is_valid_website_id =false;
        $wid = (int) $request->query->get('websiteid');
        $websiteid_sample  = (int) $config['websiteid'] ;


        if( $wid > 0 ) {
            if($websiteid_sample  ===  $wid ) {
                $is_valid_website_id = true;
            }
        }

        if( false === $is_valid_website_id) {
            $ret [0] = false;
            $ret['message'] = $validations_config [2] [ 'message'];
            $ret['code'] = $validations_config[2] ['errorno'];
            return $ret;
        }

        
        // 3 offerwow-03 uid会员不存在
        $is_valid_uid= false;
        $uid =  (int) $request->query->get('memberid');
        if( $uid  > 0) {
            $u = $this->em->getRepository("JiliApiBundle:User")->findOneById($uid);
            if(! is_null($u) ) {
                $is_valid_uid = true;
            }
        }
         

        if(false === $is_valid_uid) {
            $ret [0] = false;
            $ret['message'] = $validations_config [3] [ 'message'];
            $ret['code'] = $validations_config[3] ['errorno'];
            return $ret;
        } else {
            $ret['data'][ 'uid'] = $uid;
            $ret['data'][ 'user'] = $u;
        }

        //（2）immediate=0的情况下，才允许接收相同eventid的推送记录，返利 ???
        // 4 offerwow-04 已发放奖励的Eventid重复
        // todo: the eventid has completed !!
        $o = $this->em->getRepository("JiliApiBundle:OfferwowOrder")->findOneByEventid($request->query->get('eventid'));

        if( ! is_null($o) || ( is_array($o) &&  0 !== count($o)) ) {

            $is_completed  = OrderBase::isCompleted($o);


            if(  $is_completed ) {
                $ret [0] = false;
                $ret['message'] = $validations_config [4] [ 'message'];
                $ret['code'] = $validations_config[4] ['errorno'];
                return $ret;
            }

            $immeidate_request = (int ) $request->query->get('immediate'); 

            if( $immeidate_request === 1 ) {
                $ret [0] = false;
                $ret['message'] = $validations_config [6] [ 'message'];
                $ret['code'] = $validations_config[6] ['errorno'];
                return $ret;
            }

        } 

// 5 offerwow-05 immediate=0

// 6 offerwow-06 immediate=3


        return $ret;
    }
}

