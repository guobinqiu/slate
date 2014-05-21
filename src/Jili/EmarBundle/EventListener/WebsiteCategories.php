<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Symfony\Component\Filesystem\Filesystem;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

class WebsiteCategories {

    private $logger;

    private $websiteCategoryGet;

    /**
     * 1. fetch first level category,
     * 2. fetch second level category based on first level category;
     * 3. wirte the category to cached file for next fetch.
     */
    public function fetch( ) {

    #    $cached = $this->cache_dir.DIRECTORY_SEPARATOR.'emar_website_category_'.date('Ym').'.cached';
    #    $fs = new Filesystem();

    #    if( $fs->exists($cached) ) {
    #        $web_categories = @unserialize(file_get_contents($cached));
    #    }

    #    if( !isset($web_categories) || ! is_array($web_categories) ) {
            // cats
            $wcategories_raw  = $this->websiteCategoryGet->fetch( );
            $wcats = WebCatRepository::parse( $wcategories_raw);
            #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $wcategories_raw, true));
            #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $wcats, true));
    #        @file_put_contents( $cached, serialize($web_categories) , LOCK_EX);
    #    }
        return $wcats;
    }

#    public function setCacheDir($dir) {
#        $this->cache_dir= $dir ;
#    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function setWebsiteCategoryGet(  $getter ) {
        $this->websiteCategoryGet= $getter ;
    }

}
