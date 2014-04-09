<?Php
namespace Jili\EmarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Psr\Log\LoggerInterface;
use Jili\EmarBundle\Entity\EmarProductsCroned;
use Jili\EmarBundle\Api2\Utils\PerRestrict;


class ProductsCommand extends ContainerAwareCommand 
{
    protected function configure()
    {
        $this
            ->setName('emar:products')
            ->setDescription('manager emar products with table emar_products_synced')
            ->addArgument(
                'wid',
                InputArgument::OPTIONAL,
                'the webiste id'
            )
            ->addArgument(
                'catid',
                InputArgument::OPTIONAL,
                'the category id'
            )
            ->addOption(
               'list',
               null,
               InputOption::VALUE_NONE,
               'list emar websits in table advertiserment'
            )
            ->addOption(
               'update-all-count',
               null,
               InputOption::VALUE_NONE,
               'Dry run , count emar products'
            )
            ->addOption(
               'update-all',
               null,
               InputOption::VALUE_NONE,
               'update emar products in table advertiserment'
            )
            ->addOption(
               'start',
               null,
               InputOption::VALUE_OPTIONAL,
               'start from'
            )
            ->addOption(
               'update',
               null,
               InputOption::VALUE_NONE,
               'update emar websits in table advertiserment'
            )
            ->addOption(
               'remove',
               null,
               InputOption::VALUE_NONE,
               'remove rows 1 day created 1 day ago'
            );
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $wid = $input->getArgument('wid');
        $catid = $input->getArgument('catid');

        $output->writeln('wid:'. var_export($wid, true).'; catid:'.var_export( $catid, true) );
        $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ).'wid:'.var_export($wid,true).';catid:'.var_export($catid,true));


        // evaluate how long it will takes to fetch rows.
        if ($input->getOption('update-all-count')) {

            $em  = $this->getContainer()->get('doctrine')->getManager( );

            $websites = $this->getContainer()->get('website.list_get')->fetch();

            $product_categories = $this->getContainer()->get('product.categories')->fetch();

            $cats_mixed = array();
            foreach( $product_categories['cats'] as $k => $v ) {
                $cats_mixed[$k] = $v;
                foreach( $product_categories['sub_cats'][$k] as $k1 => $v1 ) {
                    $cats_mixed[$k1] = $v1;
                }
            }

            $pr = new PerRestrict(500);

            $start = (int) $input->getOption('start'); // 断点

            $i = 0;
            $page_size = 100;

            $request_counter = 0;
            $loop_counter = count($cats_mixed) * count($websites) ;

            #$output->writeln('outer loop, $cats_mixed: '. PHP_EOL.var_export( $cats_mixed, true));
            #$output->writeln('inner loop, $websites : '. PHP_EOL.var_export( $websites, true));
            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'websites:','') ). var_export(count($websites), true));
            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'$cats_mixed:','') ). var_export(count($cats_mixed), true));
            $output->writeln('total:  '. $loop_counter  . ' = '. count($cats_mixed) .  '*'. count($websites).'( pagination excluded)' );


            
            foreach( $cats_mixed as $key =>  $cat  ) {
                $catid = $key;
                foreach($websites as $web ) {
                    $i++;
                    if( $start > $i ) {
                        $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'continue','')).$i  );
                        continue;
                    }

                    $output->writeln('-- cat name:'. $cat .' web name:' . $web['web_name']);

                    try {
                        $webid = $web['web_id'];
                        $params = array('catid'=> $catid, 'webid'=>$webid );
                        $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'$params:','') ). var_export($params, true) );
                        $productListGetter = $this->getContainer()->get('product.list_get');
                        $productListGetter->setPageSize($page_size);

                        #$productListGetter->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
                        $productListGetter->setFields('total');
                        $last = 1; $page_no = 0;
                        do {
                            $page_no++;
                            if( $page_no == 1 ) {
                                $pr->add();
                                $products = $productListGetter->fetch($params);
                                $total = $productListGetter->getTotal();
                                $str = $total.' in raw';
                                $total = ( 1800 < $total) ? 1800 : $total;
                                $str .= ', '.$total.' restricted.'.PHP_EOL;
                                if( $total > $page_size) {
                                    $last = (int) ceil( $total / $page_size );
                                }  


                                $log = sprintf("cname:%s(%s) ;wname:%s (%s) total: %d , last: %d", $cat , $key , $web['web_name'], $web['web_id'] , $total, $last ); 
                                $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). $log );

                                $output->writeln(' @'.date("Y-m-d H:i:s").' $i:'. $i. ' '. $page_no . PHP_EOL.$str.PHP_EOL );
                                $request_counter += $last;
                            } else {
                                break;
                            }

                        } while( $page_no < $last ); 

                    } catch( \Exception $e)  {
                        $output->writeln('current:'.$i. ' , page '.$page_no.' request!');
                        $output->writeln('catid:'. $catid. '; webid:'. $webid );
                    }
                }
            }

            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'total requests:', $request_counter) ) );

            $output->writeln($i. ' pair to request!');

        } else if ($input->getOption('update-all')) {

            $em  = $this->getContainer()->get('doctrine')->getManager( );
            $websites = $this->getContainer()->get('website.list_get')->fetch();

            $product_categories = $this->getContainer()->get('product.categories')->fetch();
            $cats_mixed = array();
            foreach( $product_categories['cats'] as $k => $v ) {
                $cats_mixed[$k] = $v;
                foreach( $product_categories['sub_cats'][$k] as $k1 => $v1 ) {
                    $cats_mixed[$k1] = $v1;
                }
            }

            $pr = new PerRestrict( 500 );
            $start = (int) $input->getOption('start'); // 断点
            $i = 0;
            $page_size = 100;

            $this->getContainer()->get('cron.website_and_category')->truncate();
            $this->getContainer()->get('cron.products')->truncate();

            foreach( $cats_mixed as $key =>  $cat  ) {
                $catid = $key;
                foreach($websites as $web ) {
                    $i++;
                    if( $start > $i ) {
                        $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'continue','')).$i  );
                        continue;
                    }

                    $output->writeln('-- '. $cat .' ' . $web['web_name']);

                    try {
                        $webid = $web['web_id'];
                        $params = array('catid'=> $catid, 'webid'=>$webid );
                        $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'$params:','') ). var_export($params, true) );
                        $productListGetter = $this->getContainer()->get('product.list_get');

                        $productListGetter->setPageSize($page_size);
                        $productListGetter->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
                        $last = 1; $page_no = 0;
                        do {
                            $page_no++;
                            $pr->add();
                            $output->writeln(' @'.date("Y-m-d H:i:s").' $i:'. $i. ' page_no:'. $page_no  );

                            $products = $productListGetter->fetch($params);

                            // including insert/update the table emar_websites_category_cron
                            $this->getContainer()->get('cron.products')->save($products);
                            #if(0 < count($products)) {
                            #    $output->writeln('demo , line:'. __LINE__ );
                            #    break 3; 
                            #}

                            #$logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). var_export($products, true) );
                            if( $page_no == 1 ) {
                                $total = $productListGetter->getTotal();
                                $total = ( 1800 < $total) ? 1800 : $total;
                                $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'$total','') ). var_export($total, true) );
                                if( $total > $page_size) {
                                    $last = (int) ceil( $total / $page_size );
                                    $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'$last','') ). var_export($last, true) );
                                }
                            }
                        } while( $page_no < $last ); 

                    } catch( \Exception $e)  {
                        $output->writeln('current:'.$i. ' , page '.$page_no.' request!');
                        $output->writeln('catid:'. $catid. '; webid:'. $webid );
                        //$logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'websites:','') ). var_export(count($websites), true));
                    }
                }
            }

            $this->getContainer()->get('cron.website_and_category')->duplicateForQuery();
            $this->getContainer()->get('cron.products')->duplicateForQuery();

            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'websites:','') ). var_export(count($websites), true));
            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'$cats_mixed:','') ). var_export(count($cats_mixed), true));
            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'total','')). $i  );
            $output->writeln($i. ' pair to request!');


            // whether continue?

            // break point ( $i, $j )

            // get category $i
            // get websites.list $j
            //  loop the category 


        } else if ($input->getOption('update')) {


            $em  = $this->getContainer()->get('doctrine')->getManager( );
            $params = array('catid'=> $catid, 'webid'=>$wid );
            $productListGetter = $this->getContainer()->get('product.list_get');
            $productListGetter->setPageSize(100);
            $productListGetter->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
            $products = $productListGetter->fetch($params);

            $total = $productListGetter->getTotal();
            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'total','') ). var_export($total, true) );

            $this->getContainer()->get('cron.products')->save($products);

            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). var_export($products, true) );
        } else if ($input->getOption('remove')) {
            $numDeleted = $this->getContainer()->get('cron.products')->truncate();
            $output->writeln($numDeleted. ' lines deleted!');
            // user input.
            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ) );
        } else {
            $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ) );
        }
        $output->writeln('ok');
    }
}

////              "pid": "72640487",
//                "p_name": "步步高(BBK)HCD007(6033)/(33)P/TSDL(LCD) 电话机(白)",
//                "web_id": "3414",
//                "web_name": "史泰博",
//                "ori_price": "82.0",
//                "cur_price": "82.0",
//                "pic_url": "http://S05.staples.cn:80/ftp_product_img/cn01070106024_1_enl.jpg",
//                "catid": "101030000",
//                "cname": "通讯配件",
//                "p_o_url": "http://p.yiqifa.com/n?k=MEbdCJqmrI6HWEDe1n4H2mquUZgL18H_UmUmfc673QXxMQWd3OF_RZ4_MZPEUIHLWNjmWntL6EjS6ZLErI6H6ED71ZL7WE3s6EUHWZLErJoH2mLS1N6b3OKS3mLE&e=API3432dsee&spm=1326528120671.1.1.1",
//                "short_intro": "来电显示电话 全国通用来电显示 并可查阅、删除、快速回拨 3级超强防雷击线路设计  机械长途锁 R键(时间可设)  规格 颜色:雅白色 频率:*** 内置式留言系统:*** 来电显示:全国通用来电显示 免提拨号:*** 快速拨号:IP拨号功能 电话号码储存:60组来电信息和15组去电信息自动存贮 耳机插孔:*** 暂停功能:*** 挂壁式:*** 电话转接:*** 液晶显示:5级显示屏亮度调节 3方会议:是"
//            }]
            //todo: total
            //        foreach($products as $pdt) {
            //            $emarProduct = $em->getRepository('JiliEmarBundle:EmarProductsCroned')->findOneByPid($pdt['pid']);
            //            if( ! $emarProduct ) {
            //                $emarProduct = new EmarProductsCroned;
            //                $emarProduct->setPid($pdt['pid']);
            //
            //            }else {
            //                $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). var_export($emarProduct, true) );
            //            }
            //
            //            $emarProduct->setPName($pdt['p_name']);
            //            $emarProduct->setWebId($pdt['web_id']);
            //            $emarProduct->setWebName($pdt['web_name']);
            //            $emarProduct->setOriPrice($pdt['ori_price']);
            //            $emarProduct->setCurPrice($pdt['cur_price']);
            //            $emarProduct->setPicUrl($pdt['pic_url']);
            //            $emarProduct->setCatid($pdt['catid']);
            //            $emarProduct->setCname($pdt['cname']);
            //            $emarProduct->setPOUrl($pdt['p_o_url']);
            //            $emarProduct->setShortIntro($pdt['short_intro']);
            //            $em->persist($emarProduct);
            //            $em->flush();
            //            $output->writeln('.');
            //        }
