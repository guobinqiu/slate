#!/usr/bin/perl
use strict;
use warnings;

use Time::Piece;
use Text::CSV;
use utf8;

use Data::Dumper;
use URI::Escape;
use Jili::DBConnection;
use Uri::Parser;
use Yiqifa::CpsConfirmed;

use Encode qw(from_to encode);
use URI::Escape;
use Digest::MD5 qw(md5_hex);

sub csv_parse {
# read file
    my ($files ) = @_;
    binmode(STDIN, ':encoding(utf8)');
    binmode(STDOUT, ':encoding(utf8)');
    binmode(STDERR, ':encoding(utf8)');
    foreach my $file ( @$files) {
        print $file,"\n";
# parse line
        my $csv = Text::CSV->new ({auto_diag => 1, binary=>1,allow_whitespace=>1 })  # should set binary attribute.
            or die "Cannot use CSV: ".Text::CSV->error_diag ();
        open(my $fh, '<:encoding(utf8)', $file ) or die "Could not open '$file' $!\n";
        my $i = 0;
        my $title=[];
        while ( my $row = $csv->getline( $fh ) ) {
            if($i==0) {
                push $title, @$row;
                $i++;
                next;
            }
            if (length($row->[0]) > 0 && $row->[0] eq '合计') {
                next;
            };

            if( $row->[13] ne '未确认') {
                next;
            }

            print '   ' ,$i,":\n";
            print '        ', $title->[$_] ,':  ', $row->[$_],"  \n", foreach  (keys  @$row);
            print "\n";
            print "--------------------";
            print "\n";

            $i++;
        }

        $csv->eof or $csv->error_diag();

        close($fh)|| warn "close failed: $!";
    }
# build query

}

sub fetch_confirmed {
    # read file
    my ($files ) = @_;
    binmode(STDIN, ':encoding(utf8)');
    binmode(STDOUT, ':encoding(utf8)');
    binmode(STDERR, ':encoding(utf8)');

    my $t1 = localtime->strptime('2015-02-27 00:00:00', "%Y-%m-%d %H:%M:%S");
    my $t2 = localtime->strptime('2015-03-15 00:00:00', "%Y-%m-%d %H:%M:%S");

    foreach my $file ( @$files) {
        print ' loading confirmed data ', $file,"\n";
        my $csv = Text::CSV->new ({auto_diag => 1, binary=>1,allow_whitespace=>1 })  # should set binary attribute.
            or die "Cannot use CSV: ".Text::CSV->error_diag ();
        open(my $fh, '<:encoding(utf8)', $file ) or die "Could not open '$file' $!\n";
        my $i = 0;
        my $title=[];
        while ( my $row = $csv->getline( $fh ) ) {
            if($i==0) {
                push $title, @$row;
                $i++;
                next;
            }

            if (length($row->[0]) > 0 && $row->[0] eq '合计') {
                $i++;
                next;
            };

            # filter row with confirm_time being null. Not null , [ $t1, $t2]
            if( ! defined($row->[10]) ) {
                $i++;
                next;
            } 

            my $tx = localtime->strptime($row->[10], "%Y-%m-%d %H:%M:%S");
            if( $tx  <  $t1 or  $t2  <= $tx )  {
                $i++;
                next;
            }

            # query the emar_api_return,  

            my $order_time = $row->[4];
            $order_time = uri_escape($order_time);
            $order_time =~ s/%20/\+/;

            my $order_no = $row->[5];
            $order_no =~ s/\'//;

            my $prod_money = $row->[9];
            $prod_money =~ s/(\d+\.\d{1})\d*/$1/;

            # build the emar_api_return.content condition 
            my $prod_id = $row->[7];
            chomp($prod_id);
            $prod_id=( length($prod_id) == 0) ? '%': $prod_id;

            my $sql = qq{SELECT * FROM emar_api_return WHERE content like "%unique_id=%&create_date=%&action_id=$row->[0]&action_name=%&sid=458631&wid=732204&order_no=$order_no&order_time=$order_time&prod_id=$prod_id&prod_name=%&prod_count=%&prod_money=$prod_money%&feed_back=$row->[17]&status=%&comm_type=%&commision=%&chkcode=%&prod_type=%&am=%&exchange_rate=%&superrebate=%"};

            my $database = Jili::DBConnection->instance(('root','ecnavi','zili_dev4','192.168.1.235'));
            #my $database = Jili::DBConnection->instance();
            my $dbh = $database->{dbh};

            my $sth=$dbh->prepare($sql);
            $sth->execute();
            $dbh->commit;
            my $api_return_hash_ref = $sth->fetchall_hashref(qw(id)) ;
            my $j = 0;
            $j += scalar keys %$api_return_hash_ref ;
            if ( $j == 0  ) {
# $j== 0: 19
                # no rows found, rebuild the query condition, removed the prod_money
#####                my $sql_0 = qq{SELECT * FROM emar_api_return WHERE content like "%unique_id=%&create_date=%&action_id=$row->[0]&action_name=%&sid=458631&wid=732204&order_no=$order_no&order_time=$order_time&prod_id=%&prod_name=%&prod_count=%&prod_money=%&feed_back=$row->[17]&status=%&comm_type=%&commision=%&chkcode=%&prod_type=%&am=%&exchange_rate=%&superrebate=%"};
#####                my $sth_0 =$dbh->prepare($sql_0);
#####                $sth_0->execute();
#####                $dbh->commit;
#####                my $api_return_hash_ref_0 = $sth_0->fetchall_hashref(qw(id)) ;
#####                my $k = 0;
#####                $k += scalar keys %$api_return_hash_ref_0 ;
#####
#####                if($k == 0  ) {
###### k==0 : 11
                print '  QUERY||| ',$sql,"\n";
#####                    print '  QUERY||| ',$sql_0,"\n";
                print '  NONE|||$i= ' ,$i,":\n";
                print '  NONE||||||', $title->[$_] ,':  ', $row->[$_],"  \n", foreach  (keys  @$row);
                if('无效' ne $row->[13] ) {
                    my $qs = buildQueryStringForNone($row);
                    print '   WGET|_|',$qs,"\n";
                }

                print "\n --------------------\n";
#####                }
#####
                next;

            } elsif ( $j > 1 ) {
                my $l = 0;
                foreach my $id (keys %$api_return_hash_ref) {
                    my $qs = Uri::Parser::parseQueryString($api_return_hash_ref->{$id}->{content});
                    my $hist =  queryOrderAndTaskByUniqueId($qs->{unique_id}, $l);

                    if($hist == 0 ) {
                        $l++;
                        print '   ', $id,  ' ' , $api_return_hash_ref->{$id}->{content} ,"\n";
                        print "valid uniqu_id_$l: ", $qs->{unique_id},"\n";
                    }
                }

                print '  QUERY||| ',$sql,"\n";
                print '  DUP|||$i= ' ,$i,":\n";
                print '  DUP||||||', $title->[$_] ,':  ', $row->[$_],"  \n", foreach  (keys  @$row);
                print "\n *  *  *  *  *  *  *  *  *  *  *  *  * -";
                print " *  *  *  *  *  *  *  *  *  *  *  *  * -\n";
            } elsif ($j == 1 ) {

# $j == 1:
                my @keys = keys %$api_return_hash_ref ;
                my $api_return_id =  $keys[0];

                print $api_return_id, "\n";

                my $content =  $api_return_hash_ref->{$api_return_id}->{content};

                #my $api_exists_qs =  Uri::Parser::parseQueryStringRaw($content);
                #my $unique_id = $api_exists_qs->{unique_id} ;

                #my $query = $api_exists_qs;

                #print Dumper($query);
                # replace with confirmed Data with previous 
                # regenerate the checkcode
                my $status = '';
                if('有效' eq  $row->[13]) {
                    $status = 'status=A';
                } elsif ('无效' eq $row->[13]) {
                    $status = 'status=F';
                } else {
                    print 'ignore ',$row->[13],"\n";
                    next;
                }

                $content =~ s/status=R/$status/;

                #my $order_time = uri_unescape(  $query->{order_time});
                #$order_time = uri_unescape($order_time);
                #$order_time =~ s/\+/ /;
                #$query->{chkcode} = generateChkcode($query->{action_id},
                #    $query->{order_no},
                #    $query->{prod_money},
                #    $order_time,
                #    $query->{wid}
                #); 

                #my $query_keys = [qw(unique_id create_date action_id action_name sid wid order_no order_time prod_id prod_name prod_count prod_money feed_back status comm_type commision chkcode prod_type am exchange_rate superrebate)];

                #my $qs = '';
                #my $i = 0;
                #foreach my $x (@$query_keys) {
                #    $qs .= $x.'='. $query->{$x}. '&';
                #}

                print '   WGET|||',  $content, "\n";

                # unique_id=165e4ac022865ebac2d1a8b5faf12ee3&create_date=2015-03-14+16%3A50%3A48&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=732204&order_no=8788744088&order_time=2015-03-14+16%3A48%3A54&prod_id=&prod_name=1357888&prod_count=1&prod_money=472.0&feed_back=1068849&status=R&comm_type=0&commision=4.8&chkcode=e4582ec5c725beb419f8dceb7da8ebf2&prod_type=0&am=0.0&exchange_rate=0.0&superrebate=

                print "\n- - - - - - - - - - - - - - - - - - - - ";
                print "- - - - - - - - - - - - - - - - - - - - ";
                print "- - - - - - - - - - - - - - - - - - - - \n";
            } 

            $i++;
        }

        $csv->eof or $csv->error_diag();

        close($fh)|| warn "close failed: $!";
    }
# build query
}

sub buildQueryStringForNone 
{
    binmode(STDIN, ':encoding(utf8)');
    binmode(STDOUT, ':encoding(utf8)');
    binmode(STDERR, ':encoding(utf8)');

    my ( $row) = @_;

    my $order_time = $row->[4];
    $order_time = uri_escape($order_time);
    $order_time =~ s/%20/\+/;

    my $order_no = $row->[5];
    $order_no =~ s/\'//;

    my $prod_money = $row->[9];
    $prod_money =~ s/(\d+\.\d{2})\d*/$1/;

    my $create_date = uri_escape(localtime->strftime('%Y-%m-%d %H:%M%S'));
    $create_date =~ s/%20/+/;

    my $query = {};
    $query->{unique_id} = md5_hex(time());
    $query->{create_date} = $create_date; 
    $query->{action_id}= $row->[0];

    my $action_name = $row->[1];
    $action_name = encode('gbk',$row->[1]);
    $action_name = uri_escape($action_name);
    $query->{action_name} =

    $query->{sid}= 458631;
    $query->{wid}= 732204;
    $query->{order_no} = $order_no;
    $query->{order_time} =$order_time ; 
    $query->{prod_id} = $row->[7];
    $query->{prod_name} = '';
    $query->{prod_count} = $row->[8];
    $query->{prod_money} = $prod_money;
    $query->{feed_back} = $row->[17];

    if('有效' eq  $row->[13]) {
        $query->{status} = 'A';
    } elsif ('无效' eq $row->[13]) {
        $query->{status} = 'F';
    } else {
        print 'ignore ',$row->[13],"\n";
        return 1;
    }

    my $type =$row->[6]; 
    print $type, "\n";
    $type = encode("gbk", $type);
    $type = uri_escape($type);
#$type= uri_escape_utf8($type);
    $query->{comm_type} = $type;
    my $commision = $row->[15];
    $commision =~ s/(\d+.\d{2})\d*/$1/;
    $query->{commision} = $commision;
    $query->{prod_type} = $type;
    my $am = $row->[12];
    $am =~ s/(\d+.\d{2})\d*/$1/;
    $query->{am} = $am;
    $query->{exchange_rate} = '0.0';

    $query->{superrebate}='';
    $query->{chkcode} = generateChkcode($query->{action_id},
        $query->{order_no},
        $query->{prod_money},
        $row->[4],
        $query->{wid}
    ); 

    my $query_keys = [qw(unique_id create_date action_id action_name sid wid order_no order_time prod_id prod_name prod_count prod_money feed_back status comm_type commision chkcode prod_type am exchange_rate superrebate)];

    print '   ',$_," => ", $query->{$_}, "\n" for(keys %$query);
    my $qs = '';
    my $i = 0;
    foreach my $x (@$query_keys) {
            $qs = $qs. $x. '='. $query->{$x}. '&';
    }
    return $qs;
    # unique_id=165e4ac022865ebac2d1a8b5faf12ee3&create_date=2015-03-14+16%3A50%3A48&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=732204&order_no=8788744088&order_time=2015-03-14+16%3A48%3A54&prod_id=&prod_name=1357888&prod_count=1&prod_money=472.0&feed_back=1068849&status=R&comm_type=0&commision=4.8&chkcode=e4582ec5c725beb419f8dceb7da8ebf2&prod_type=0&am=0.0&exchange_rate=0.0&superrebate=
}

sub generateChkcode {
    my ($action_id, $order_no, $prod_money, $order_time, $wid) = @_;
    my $DataSecret ='';
    if($wid eq '732204' ) {
        $DataSecret= '139216929186018017';
    } elsif($wid eq '708089')  {
        $DataSecret= '139216929186018017';
    } else {
        return '';
    }

    #MD5(action_id+order_no+prod_money+order_time+DataSecret)
    return md5_hex($action_id.$order_no.$prod_money.$order_time.$DataSecret);
}

# return 0 , a valid unique_id , has( order ,task records)
# return 1 no order record
# return 2 no task record
sub queryOrderAndTaskByUniqueId {
    my ($ocd, $seq_nu ) = @_;
# 
    my $database = Jili::DBConnection->instance(('root','ecnavi','zili_dev4','192.168.1.235'));
    #my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $sth_order  =$dbh->prepare(qq{SELECT * FROM emar_order where ocd =?});
#print $ocd,"\n";
    $sth_order->execute(($ocd));
    $dbh->commit;
    my $order_hash_ref = $sth_order->fetchrow_hashref() ;

    if(! defined($order_hash_ref) ) {
        return 1;
    }

    my $task_table = 'task_history0'. ($order_hash_ref->{user_id} % 10);

#print $task_table,"\n";
    my $sth_task =$dbh->prepare(qq{SELECT * FROM $task_table where category_type = ? and order_id  =?});
    $sth_task->execute((19, $order_hash_ref->{id}));
    $dbh->commit;
    my $task_hash_ref = $sth_task->fetchrow_hashref() ;

    if( ! defined($task_hash_ref ) ) {
        return 2;
    };

    print '   ',$seq_nu,$task_table,'.status: ', $task_hash_ref->{status},"\n" ;
    return 0;
}


my $confirmed_data_dir = './status_recallback';
my $files = Yiqifa::CpsConfirmed::get_confirmed_utf8_filelist($confirmed_data_dir);
print Dumper($files);
fetch_confirmed($files);

__END__
 
my $files = [
  'CpsEffectConfirmData1429061431686_utf8.csv',
  'CpsEffectConfirmData1429061453865_utf8.csv',
  'CpsEffectConfirmData1429061473035_utf8.csv'
  ];
csv_parse($files);

 unique_id=165e4ac022865ebac2d1a8b5faf12ee3&create_date=2015-03-14+16%3A50%3A48&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=732204&order_no=8788744088&order_time=2015-03-14+16%3A48%3A54&prod_id=&prod_name=1357888&prod_count=1&prod_money=472.0&feed_back=1068849&status=R&comm_type=0&commision=4.8&chkcode=e4582ec5c725beb419f8dceb7da8ebf2&prod_type=0&am=0.0&exchange_rate=0.0&superrebate=
   1:
       0   活动ID:  5402  
        1  活动名称:  美团网CPS  
        2  网站ID:  732204  
        3  网站名称:  购物客91jili  
        4  下单时间:  2015-01-01 00:23:23  
        5  订单编号:  890134907-2'  
        6  商品类别:  O  
        7  商品编号:  24975453  
      8  收订订单数:  1  
      9  收订订单金额:  9.90000  
      10  确认时间:  2015-03-04 16:59:25  
       11 有效订单数:  1  
       12 有效订单金额:  9.90000  
       13 业绩状态:  有效  
       14 业绩类型:  正常  
       15 预计佣金:  0.11880  
       16 确认佣金:  0.00000  
       17 反馈标签:  1147880  
       18 是否超级返推广:  否  


 $sql = qq{SELECT * FROM emar_apir_return WHERE content like 'unique_id=%&create_date=%&action_id=$row->[0]&action_name=%&sid=458631&wid=732204&order_no=$row->[5]&order_time=$row->[4]&prod_id=%&prod_name=%&prod_count=%&prod_money=$row->[9]&feed_back=%&status=%&comm_type=%&commision=%&chkcode=%&prod_type=%&am=%&exchange_rate=%&superrebate=%'};


 SELECT * FROM emar_api_return WHERE content like "%&action_id=297%&sid=458631&wid=732204&order_no=248024412%"


0        活动ID:  297  
1        活动名称:  携程旅行网CPS  
2        网站ID:  732204  
3        网站名称:  购物客91jili  
4        下单时间:  2014-07-14 18:29:21  
5        订单编号:  248024412'  
6        商品类别:  H  
7        商品编号:    
8        收订订单数:  1  
9        收订订单金额:  99.00000  
10        确认时间:  2015-03-03 14:24:05  
 11       有效订单数:  0  
12        有效订单金额:  0.00000  
13        业绩状态:  无效  
14        业绩类型:  正常  
15        预计佣金:  2.97000  
16        确认佣金:  0.0000  
17        反馈标签:  1064857  
18        是否超级返推广:  否  

 /emar/api/callback?unique_id=6d0f92a6db15a7a81630c9f4bee7924b&create_date=2014-07-14+18%3A29%3A33&action_id=297&action_name=%D0%AF%B3%CC%C2%C3%D0%D0%CD%F8CPS&sid=458631&wid=732204&order_no=248024412&order_time=2014-07-14+18%3A29%3A21&prod_id=&prod_name=&prod_count=1&prod_money=99.0&feed_back=1064857&status=R&comm_type=H&commision=2.97&chkcode=83e29f8288e5e686d887ffee545653b2&prod_type=H&am=0.0&exchange_rate=0.0 |

150416 17:08:32 28540 Query SELECT * FROM emar_api_return WHERE content like "%unique_id=%&create_date=%&action_id=297&action_name=%&sid=458631&wid=732204&order_no=248024412'&order_time=2014-07-14%2018%3A29%3A21&prod_id=%&prod_name=%&prod_count=%&prod_money=99.00000&feed_back=%&status=%&comm_type=%&commision=%&chkcode=%&prod_type=%&am=%&exchange_rate=%&superrebate=%"

"%unique_id=%&create_date=%&action_id=297&action_name=%&sid=458631&wid=732204&order_no=248024412'&order_time=2014-07-14%2018%3A29%3A21&prod_id=%&prod_name=%&prod_count=%&prod_money=99.00000&feed_back=%&status=%&comm_type=%&commision=%&chkcode=%&prod_type=%&am=%&exchange_rate=%&superrebate=%"


 diff: 
 datetime + than %20
 feedback use it!
 prod_money   

SELECT * FROM emar_api_return WHERE content like "%unique_id=%&create_date=%&action_id=5402&action_name=%&sid=458631&wid=732204&order_no=890134907-2&order_time=2015-01-01%2B00%3A23%3A23&prod_id=%&prod_name=%&prod_count=%&prod_money=9.90000&feed_back=1147880&status=%&comm_type=%&commision=%&chkcode=%&prod_type=%&am=%&exchange_rate=%&superrebate=%"


