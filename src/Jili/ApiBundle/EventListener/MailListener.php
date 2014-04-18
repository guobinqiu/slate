<?php
namespace Jili\ApiBundle\EventListener;

use Doctrine \ ORM \ EntityManager;
use Symfony \ Component \ DependencyInjection \ ParameterBagInterface;

/**
 *
 **/
class MailListener {
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @param  $url
	 * @param  $email
	 */
	public function sendMailForWenWenRegister($mailer, $url, $email) {
		$message = \ Swift_Message :: newInstance()
		->setSubject('积粒网-注册激活邮件-从91问问网站注册')
		->setFrom(array ('account@91jili.com' => '积粒网'))
		->setTo($email)
		->setBody('<html>' .
		' <head></head>' .
		' <body>' .
		'亲爱的' . $email . '<br/>' .
		'<br/>' .
		'  感谢您注册91问问网站的同时注册“积粒网”！<br/>请点击<a href=' . $url . ' target="_blank">这里</a>，立即激活您的帐户！<br/><br/>' .
		'  积粒网，一站式积分媒体！<br/>赚米粒，攒米粒，花米粒，一站搞定！' .
		' </body>' .
		'</html>', 'text/html');
		$flag = $mailer->send($message);
		if ($flag === 1) {
			return true;
		} else {
			return false;
		}
	}

	public function getParameter($key) {
		return $this->container_->getParameter($key);
	}

	public function setContainer($c) {
		$this->container_ = $c;
	}

}