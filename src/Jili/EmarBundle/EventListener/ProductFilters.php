<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

class ProductFilters  {

    private $logger;

    private $generalCategoryGet;
    private $websiteCateogryGet;
    private $websiteListGet;

    /**
     * new ui
     */
    public function fetchWebs( ) {

        //webs
        $webListGet  = $this->websiteListGet;
        $web_raw  = $webListGet->setFields('web_id,web_name')->fetch( );
        $webs = WebListRepository::parse( $web_raw);
        //TODO: sort the webs 
        
       #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $webs, true));

        return compact('webs');
    }
    /**
     * demo:
     */
    public function fetch( ) {

        // cats
        $categories_raw  = $this->generalCategoryGet->fetch();
        $cats = ItemCatRepository::parse( $categories_raw);

        #        $this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $cats, true));


        // wcats
        $wcategories_raw  = $this->websiteCategoryGet->fetch( );
        $wcats = WebCatRepository::parse( $wcategories_raw);
        #        $this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $wcats, true));
        //webs
        $web_raw  = $this->websiteListGet->fetch( );

        #$webs = WebListRepository::parse( $web_raw);
        $webs = WebListRepository::parseByCat( $web_raw);
        #        $this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $webs, true));

        return compact('cats','wcats','webs');
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }

    //            - [ setGeneralCategoryGet, [ @general.category_get ] ] 
    //            - [ setWebsiteCategoryGet, [ @website.category_get ] ] 
    //            - [ setWebsiteListGet, [ @website.list_get ] ] 

    public function setGeneralCategoryGet(  $getter ) {
        $this->generalCategoryGet= $getter ;
    }

    public function setWebsiteCategoryGet(  $getter) {
        $this->websiteCategoryGet = $getter;
    }

    public function setWebsiteListGet(  $getter) {
        $this->websiteListGet = $getter;
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
