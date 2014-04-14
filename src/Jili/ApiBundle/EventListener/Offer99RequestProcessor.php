<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\Offer99Order;
use Jili\ApiBundle\Util\String;

/**
 *
 **/
class Offer99RequestProcessor {
	private $em;
	private $logger;
	private $parameterBag;
	private $container_;

	private $task_logger;
	private $point_logger;

	public function __construct(LoggerInterface $logger, EntityManager $em /*, ParameterBagInterface $parameterBag*/
	) {
		$this->logger = $logger;
		$this->em = $em;
	}

	public function process(Request $request, array $config) {

		$category_type = $config['category_type'];
		$task_name = $config['name'];
		$task_type = $config['task_type'];

		$this->logger->debug('{jaord}' . __FILE__ . ':' . __LINE__ . var_export($request->query, true));

		$tid = $request->query->get('tid');
		$uid = $request->query->get('uid');
		$point = $request->query->get('vcpoints');
		$happen_time = date_create();

		$em = $this->em;

		// init log.
		$this->logger->debug('{jaord}' . __FILE__ . ':' . __LINE__ . ':HANGUP_SUSPEND');

		$order = $em->getRepository('JiliApiBundle:Offer99Order')->findOneByTid($tid);
		if (is_null($order)) {
			$is_new = true;
			// init offerorder & task history
			$order = new Offer99Order();
			// update offerorder
			$order->setUserid($uid); // order
			$order->setTid($tid); // order
			$order->setCreatedAt(date_create(date('Y-m-d H:i:s')));
			$order->setDeleteFlag(0);
			$em->persist($order);
			$em->flush();
		}

		$params = array (
			'userid' => $uid,
			'orderId' => $order->getId(),
			'taskType' => $task_type,
			'categoryType' => $category_type,
			'reward_percent' => 0,
			'task_name' => $task_name,
			'point' => $point,
			'date' => $happen_time,
			'status' => 1
		);

		// updte task_history
		$this->initTaskHistory($params);

		$user = $em->getRepository('JiliApiBundle:User')->find($uid);
		$user->setPoints(intval($user->getPoints()) + intval($point));
		$em->persist($user);
		$em->flush();

		// updte point_history
		$this->getPointHistory($user->getId(), $point, $category_type );

	}

	private function updateTaskHistory($params = array ()) {
		extract($params);
		return $this->task_logger->update($params);
	}

	private function initTaskHistory($params = array ()) {
		extract($params);
		return $this->task_logger->init($params);
	}

	private function TaskHistory($params = array ()) {
		extract($params);
		return $this->task_logger->update($params);
	}

	public function selectTaskPercent($userid, $orderId) {
		return $this->task_logger->selectPercent(array (
			'user_id' => $userid,
			'order_id' => $orderId
		));
	}

	private function getPointHistory($userid, $point, $type) {
		$this->point_logger->get(compact('userid', 'point', 'type'));
	}

	public function getParameter($key) {
		return $this->container_->getParameter($key);
	}

	public function setContainer($c) {
		$this->container_ = $c;
	}

	public function setTaskLogger(TaskHistory $task_logger) {
		$this->task_logger = $task_logger;
	}

	public function setPointLogger(PointHistory $point_logger) {
		$this->point_logger = $point_logger;
	}
}