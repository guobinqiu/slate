<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Doctrine\ORM\EntityManager;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

class ProductFilters  {

    private $logger;
    private $em;

    private $generalCategoryGet;
    private $websiteCateogryGet;
    private $websiteListGet;

    public function fetchWebsConfigged() {
        $em =$this->em;
        $webs_config = $em->getRepository('JiliEmarBundle:EmarWebsites')->getFilterWebs();
        $wids = array();
        foreach($webs_config as $web) {
            $wids [] = $web->getWebId();
        }
        $result  = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchByWebIds( $wids);

        $webs=array();
        foreach($result as $row) {
            $webs[ $row->getWebId() ] = $row;
        }
        return compact('webs');
    }

    /**
     * for new ui:product/search
     * #$filters_of_webs = $this->get('product.filters')->fetchWebsByParams( array( 'wids'=> $products_webids)  );
     */
    public function fetchWebsByParams($params = array() ) {
        if( count($params) == 0 || ! isset($params['wids']) || empty($params['wids']) ){
            return $this->fetchWebs();
        }
        //extract($params);
        //webs
        $webListGet  = $this->websiteListGet;
        $web_raw  = $webListGet->setFields('web_id,web_name,web_o_url,commission')->fetch( );

        $webs = WebListRepository::parse( $web_raw, $params);
        return compact('webs');
    }

    /**
     *  product/retrieve 
     * return array('webs'=> );
     */
    public function fetchWebs( ) {
        $webListGet  = $this->websiteListGet;
        $web_raw  = $webListGet->setFields('web_id,web_name,web_o_url,commission')->fetch( );
        $webs = WebListRepository::parse( $web_raw);
       #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $webs, true));
        return compact('webs');
    }

    /**
     * return array('product_webs'=> );
     */
    public function fetchWebsByProducts( $products ) {
        $webListGet  = $this->websiteListGet;
        $web_raw  = $webListGet->setFields('web_id,web_o_url,commission')->fetch( );
        $product_webs = WebListRepository::parse( $web_raw);
        $this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $product_webs, true));
        return compact('product_webs');
    }

    /**
     * demo return all websites lists:
     */
    public function fetch( ) {
        //pdt cats
        $categories_raw  = $this->generalCategoryGet->fetch();
        $cats = ItemCatRepository::parse( $categories_raw);
        $this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $cats, true));
        // web cats
        $wcategories_raw  = $this->websiteCategoryGet->fetch( );
        $wcats = WebCatRepository::parse( $wcategories_raw);
        $this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $wcats, true));
        // webs
        $web_raw  = $this->websiteListGet->fetch( );
        $webs = WebListRepository::parseByCat( $web_raw);
        #$webs = WebListRepository::parse( $web_raw);
        # $this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $webs, true));
        return compact('cats','wcats','webs');
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function setGeneralCategoryGet(  $getter ) {
        $this->generalCategoryGet= $getter ;
    }

    public function setWebsiteCategoryGet(  $getter) {
        $this->websiteCategoryGet = $getter;
    }

    public function setWebsiteListGet(  $getter) {
        $this->websiteListGet = $getter;
    }

    public function setEntityManager( EntityManager $em) {
        $this->em= $em;
    }
}
/*
        "web_list": {
            "web": [{
                "web_id": "3659",
                "web_name": "雅昌影像",
                "web_catid": "24",
                "logo_url": "http://image.yiqifa.com/ad_images/reguser/24/4/60/1376643810386.jpg",
                "web_o_url": "http://p.yiqifa.com/n?k=2mLErnWe6nDOrI6HCZg7Rnu_fmUmUSebRcgsRIeEYOsH2mLErntmWl2mrnzSWn2ernXH2mq_rI6H6E4b3NRFMEPH5toARcMJrj--&e=APImemberId&spm=139216929186018017.1.1.1",
                "commission": "10.5%"
            },

*/
