<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

/**
 *
 **/
class Offer99RequestValidation {
	private $logger;
	private $em;
	public function __construct(LoggerInterface $logger, EntityManager $em) {
		$this->logger = $logger;
		$this->em = $em;
	}

	/**
	 *
	 * @return void
	 **/
	public function validate(Request $request, array $config) {

		$ret = array (
			'valid_flag' => true,
			'code' => ''
		);

		$validations_config = $config['validations'];

		//uid 合作客户的玩家（用户）ID
		//vcpoints 虚拟货币的数量
		//tid 32位字符串，合作客户需要记录并且验证唯一性，避免重复发送
		//offer_name 玩家参与任务的任务名称
		//pass 密码验证
		$uid = $request->query->get('uid');
		$vcpoints = $request->query->get('vcpoints');
		$tid = $request->query->get('tid');
		$pass = $request->query->get('pass');

		//1.缺少参数(检查参数是否传递,tid是否为32位)
		$tid_length = strlen($tid);
		if (empty ($uid) || empty ($vcpoints) || empty ($pass) || $tid_length != 32) {
			$ret['valid_flag'] = false;
			$ret['code'] = '1001';
			return $ret;
		}

		//2.密码验证不通过$pwd = $uid.$vcpoints.$tid.$key;
		$pwd = $uid . $vcpoints . $tid . $config['key'];
		$pwd_md5 = md5($pwd);
		if ($pwd_md5 != $pass) {
			$ret['valid_flag'] = false;
			$ret['code'] = '1002';
			return $ret;
		}

		//3.tid重复
		$o = $this->em->getRepository("JiliApiBundle:Offer99Order")->findOneByTid($tid);
		if (!is_null($o)) {
			$ret['valid_flag'] = false;
			$ret['code'] = '1003';
			return $ret;
		}

		//4.vcpoints超过最大限额

		//5.uid不存在
		$u = $this->em->getRepository("JiliApiBundle:User")->findOneById($uid);
		if (is_null($u)) {
			$ret['valid_flag'] = false;
			$ret['code'] = '1005';
			return $ret;
		}

		//6.非法IP

		return $ret;
	}
}