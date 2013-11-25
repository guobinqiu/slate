<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class  CardRecordController extends Controller
{
	/**
     * @Route("/index", name="_cardRecord_index")
     */
    public function indexAction(){ 
        exit;
        /*
    	$iframeurl = 'https://entry.eightbiz.net/login';
    	$provider_key = 'jili';
    	$provider_secret = 'DXl1nnDbU3dAHXQdttSHzcZQVK4MWVOT';
    	$uid = $this->get('request')->getSession()->get('uid');
        if(!$uid){
           return $this->redirect($this->generateUrl('_user_login'));
        }
		$iframeparam = 'method=HMAC-SHA1&nonce='.rand(100000,900000).'&provider_key=jili&timestamp='.(microtime(true)).'&version=1.0&x_partner_identifier=jili&x_user_identifier='.$uid;
		$encodedstring = 'GET&'.urlencode($iframeurl).'&'.urlencode($iframeparam);
		$signature = urlencode(base64_encode(hash_hmac('sha1', $encodedstring, 'DXl1nnDbU3dAHXQdttSHzcZQVK4MWVOT',true)));
		$url = $iframeurl.'?'.$iframeparam.'&signature='.$signature;
        return $this->render('JiliApiBundle:CardRecord:index.html.twig',array('url'=>$url));
        */
    }

}