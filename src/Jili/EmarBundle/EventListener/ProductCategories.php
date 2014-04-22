<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Symfony\Component\Filesystem\Filesystem;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

class ProductCategories {

    private $logger;

    private $generalCategoryGet;

    /**
     * 1. fetch first level category,
     * 2. fetch second level category based on first level category;
     * 3. wirte the category to cached file for next fetch.
     * $force always do remote request when is set ture, in another words, if force is false the cache accessing is allowed.
     */
    public function fetch( $force = false ) {

        $cached = $this->cache_dir.DIRECTORY_SEPARATOR.'emar_product_category_'.date('Ym').'.cached';

        $fs = new Filesystem();

        if(! $force &&  $fs->exists($cached) ) {
            $prod_categories = @unserialize(file_get_contents($cached));
        }

        if( !isset($prod_categories) || ! is_array($prod_categories) || ! isset($prod_categories['cats']) || ! isset($prod_categories['sub_cats']))  {
            // cats
            $categories_raw  = $this->generalCategoryGet->fetch();

            $cats = ItemCatRepository::parse( $categories_raw);

            #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $cats, true));

            $sub_cats = array();

            foreach( $categories_raw as $cat ) {

                $cid = $cat['catid'];

                $params = array('parent_id' => $cid);

                $sub_cats_raw = $this->generalCategoryGet->fetch($params);

                $sub_cats[ $cid ] =  ItemCatRepository::parse( $sub_cats_raw);

                #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $params, true));
            }

            $prod_categories = compact('cats', 'sub_cats');

            @file_put_contents( $cached, serialize($prod_categories) , LOCK_EX);
        }
        return $prod_categories;
    }

    public function setCacheDir($dir) {
        $this->cache_dir= $dir ;
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }



    public function setGeneralCategoryGet(  $getter ) {
        $this->generalCategoryGet= $getter ;
    }

}
