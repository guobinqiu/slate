<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\BackendBundle\Controller\IpAuthenticatedController;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;




/**
 * @Route("/admin/cachepanel",requirements={"_scheme"="https"})
 */
class AdminCachePanelController extends Controller implements  IpAuthenticatedController
{
    /**
     * @Route("/")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        // sort by size desc /asc , access time
        // limit 

        $cache_dir = $this->container->getParameter('cache_data_path');

        // list some cache info

        // all cache size not support by php 

        $finder = new Finder();

        if( file_exists($cache_dir)) {
            $iterator = $finder
                ->files()
                ->name('*.cached')
                ->depth(0)
                ->in($cache_dir);
        } else {
            $iterator = array();
        }

        $logger = $this->get('logger');

        $files = array();
        $regex = "/\.[0-9a-f]{32}\.(cached)$/";

        foreach($iterator as $file ) {
            $f = preg_replace( $regex, '.*.$1' , $file->getFilename() ); 
            if( isset($files[$f])) {

                $files[$f] ['size']  += $file->getSize();
                $files[$f] ['ATime']  = $files[$f] ['ATime']  < $file->getATime()  ? $file->getATime(): $files[$f] ['ATime'];


                $files[$f]['count']++;

            } else {
                $files[$f] =array( 'size'  => $file->getSize(),
                    'count'=> 1,
                    'ATime'=> $file->getATime());

            }

        }
        $total = count($iterator);
        $form= $this->createDeleteForm();
        return array( 'total'=> $total, 'files'=> $files, 'form'=> $form->createView() ); 

    }

    /**
     * @Route("/remove/{filenames}", requirements={"filenames":"[\w,]+"} , defaults={"filenames": ""} )
     * @Method("DELETE")
     * @Template
     */
    public function doRemoveAction($filenames) 
    {
        // batch remove
        $request = $this->getRequest();
        $logger = $this->get('logger');
        ;

        if($request->getMethod() === 'DELETE') {
            $form=$this->createDeleteForm();
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if( isset($data['filenames']  ) ) {
                    $files = json_decode($data['filenames'], true);
                    $cache_dir = $this->container->getParameter('cache_data_path');
                    $finder = new Finder();
                    foreach($files as $file) {
                        $iterator = $finder
                            ->files()
                            ->name($file)
                            ->depth(0)
                            ->in($cache_dir);

                        $filesystem = new Filesystem();
                        foreach($iterator as $found) {
                            $filesystem->remove($found->getPath(). DIRECTORY_SEPARATOR.$found->getFilename() );
                        }

                        $this->get('session')->getFlashBag()->add(
                            'notice',
                            'Your changes were saved!'
                        );
                    }
                }
            }
        }

        return $this->redirect($this->generateUrl('jili_emar_admincachepanel_index'));
    }

    /**
     * @param mixed $filename The entity $filename 
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($filename='')
    {
//<input type="hidden" name="_method" value="DELETE" id="_method"> 
        return $this->createFormBuilder(array('filename' => $filename))
            ->add('filenames', 'hidden')
            ->getForm()
        ;
    }
}
