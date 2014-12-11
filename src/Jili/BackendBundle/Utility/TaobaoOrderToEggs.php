<?php
namespace Jili\BackendBundle\Utility;


class TaobaoOrderToEggs
{

    /**
     *
     * 购物金额 奖券数
     * 10元 1
     * 20元 2
     * 50元 3
     * 100元 4
     * 150元 5
     * 每增加50元 奖券数+1
     */
    static public function caculateEggs($paid = 0) 
    {
        if(  $paid <= 0) {
            return array( 'left'=> 0, 'count_of_eggs'=> 0);
        } 

        $num_eggs = 0 ;
        if($paid >= 150  ) { // [150, infinit)
            $higher = $paid - 150 ;
            $quotient = floor($higher / 50);
            return array( 'left'=>  $higher - $quotient * 50 ,
                'count_of_eggs'=>  $quotient + 5);
        } else if( $paid >= 100 ) { // [ 100, 150)
            return array( 'left'=> $paid - 100, 'count_of_eggs'=> 4);
        } else if ( $paid >= 50 ) { // [ 50, 100)
            return array( 'left'=> $paid - 50, 'count_of_eggs'=> 3);
        } elseif( $paid >= 20 ) {
            return array( 'left'=> $paid - 20, 'count_of_eggs'=> 2);
        } elseif( $paid >= 10 ) {
            return array( 'left'=> $paid - 10, 'count_of_eggs'=> 1);
        }

        return array( 'left'=> $paid, 'count_of_eggs'=> 0);
    }

    /**
     * @param float $offcut [0, +infinit) 
     * @return 50  when $offcut >= 150
     */
    static public function lessToNext( $total_paid = 0 ) 
    {
        if( $total_paid < 10 ) {
            return 10 - $total_paid;
        } elseif ( $total_paid < 20 )  {
            return 20 - $total_paid;
        } elseif($total_paid < 50 ) {
            return 50 - $total_paid;
        } elseif($total_paid < 100 ) {
            return 100 - $total_paid;
        } elseif($total_paid < 150 ) {
            return 150 - $total_paid;
        }

        $higher =  $total_paid - 150;
        $quotient = floor($higher / 50);

        return  50 * ( 1 + $quotient ) - $higher;
    }
}
