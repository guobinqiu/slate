<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Form\Type\SearchGeneralType;

use Jili\EmarBundle\Entity\EmarWebsites;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/product", requirements={"_scheme" = "http"})
 */
class ProductController extends Controller
{

    /**
     * @Route("/recommendByWeb/{wid}")
     * @Template();
     */
    public function recommendByWebAction($wid)
    {
        $request = $this->get('request');
        $logger= $this->get('logger');
        $params = array( 'webid'=> $wid,'page_no'=>1);
        $productRequest = $this->get('product.list_get');
        $productRequest->setPageSize(12);
        $products = $productRequest->fetch( $params);
        return compact(/*'total',*/'products');
    }
}
