#!/usr/bin/perl

use warnings;
use strict;

use Data::Dumper;
use YAML::Syck;

use Time::Piece;

use HTTP::Request;
use HTTP::Response;
use HTTP::Cookies;
use HTTP::Headers;
use HTTP::Cookies::Netscape;
use LWP::ConnCache;
use LWP::UserAgent;
#use Crypt::SSLeay;
#use Net::SSL (); # From Crypt-SSLeay
use File::Basename;
use File::Path qw(make_path);
use File::Spec;

use Spreadsheet::ParseExcel;
use Spreadsheet::XLSX;

use utf8;
use List::MoreUtils qw(uniq);
use Jili::DBConnection;
use HTML::TreeBuilder::XPath;
use Digest::SHA qw(sha256_hex);
use HTML::Entities;
use Text::CSV;

#$ENV{PERL_LWP_SSL_VERIFY_HOSTNAME} = 0;
$ENV{HTTPS_CA_FILE}   = '/tmp/certs/CHANET.COM.CN';
$ENV{HTTPS_CA_DIR}    = '/tmp/certs/';
$ENV{HTTPS_DEBUG} = 1;


# 取商家列表
sub fetch_xls_sites {
    my $config  = shift;
    print $config->{cookie_file},"\n" ;
    my $url = $config->{xls_sites}->{url};
    my $output =  $config->{xls_sites}->{output};


    print $url,"\n";
    my $cookie_jar = HTTP::Cookies::Netscape->new(
        file => $config->{cookie_file} 
    );

    my $headers = HTTP::Headers->new(
        Host=> 'www.chanet.com.cn', 
    );

    my $ua = LWP::UserAgent->new();

    $ua->agent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:36.0) Gecko/20100101 Firefox/36.0');
    $ua->timeout(40);
    $ua->cookie_jar($cookie_jar);
    $ua->default_header($headers );

    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;

    print $output, "\n";
    my $response = $ua->post($url);#//,':content_file' => $output);

    print $response->headers->as_string,"\n";
    die "fetch comm html failed " unless $response->status_line;
}

# 取captcha图
sub gen_captcha {
    my $config = shift;
    my $cookie_jar = HTTP::Cookies::Netscape->new(
        file => $config->{cookie_file} ,
        autosave => 1,
    );

    my $cache = LWP::ConnCache->new();
    my $ua = LWP::UserAgent->new(
        'conn_cache'=> $cache
    );
    $ua->agent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:36.0) Gecko/20100101 Firefox/36.0');
    $ua->timeout(40);
    $ua->cookie_jar($cookie_jar);


    my $url_image = 'http://www.chanet.com.cn/captcha.cgi?'. int(rand()*100 +0.4999) ;
print $url_image, "\n";

    my $res = $ua->get($url_image);
    if(!$res->is_success){
        return 0;
    }
    open(FILE_HANDLE,'>/tmp/img.jpg');
    binmode FILE_HANDLE;
    print FILE_HANDLE $res->content;
    close FILE_HANDLE;
}



  
sub login {
    my $url = 'https://www.chanet.com.cn/partner/login.cgi';
    my $date = localtime->strftime("%Y%m%d");
    my $cookie_file = "/tmp/chanet/cookies_$date.txt";
    my $cookie_jar = HTTP::Cookies->new(
        'file' => $cookie_file,
        'autosave'=>1,
        'ignore_discard'=> 1
    );

    # todo: check the expire date
    
    my $cache = LWP::ConnCache->new();
    my $ua = LWP::UserAgent->new(
        'show_progress' => 1,
        'agent'=>'Mozilla/4.73 [en] (X11; U; Linux 2.2.15 i686)',
        'timeout'=> 240,
        'cookie_jar'=> $cookie_jar,
        'conn_cache'=> $cache
    );
    my $response =  $ua->get($url );
    print $response->headers->as_string,"\n";
    #print $response->content;
    print "\n";

    my $url_image = 'http://www.chanet.com.cn/captcha.cgi?'. int(rand()*100 +0.4999) ;
    print $url_image, "\n";
    my $res = $ua->get($url_image);
    if(!$res->is_success){
        return 0;
    }
    open(FILE_HANDLE,'>/tmp/img.jpg');
    binmode FILE_HANDLE;
    print FILE_HANDLE $res->content;
    close FILE_HANDLE;

    print "input the /tmp/img.jpg: ","\n";
    my $captcha =  <STDIN>; 
    chomp ($captcha);    

    print $captcha,"\n";
    $response = $ua->post( $url, [
            'confirm'=> '1',
            'p'=> '',
            'login_name'=> 'voyage',
            'login_pass'=> 'jilijili',
            'login_week'=> '1',
            'validate_code'=> $captcha,
        ] );

    print $response->headers->as_string,"\n";
    die "download csv file failed " unless $response->status_line;

    if( $response->content =~ m/验证码错误，请重试/ ) {
        print "验证码错误，请重试\n";
        return ;
    }

    $url = 'https://www.chanet.com.cn/partner/get_all_links.cgi?pmid=&search_type=2&search_as_id=480534&link_type=2&act-type=on&category=0';
#           https://www.chanet.com.cn/partner/get_all_links.cgi?pmid=&search_type=2&search_as_id=480534&link_type=2&act-type=on&category=0
#                                                               pmid=&search_type=2&search_as_id=480534&link_type=2&act-type=on&category=0
    my $output= "/tmp/chanet/promotio_linkYYYY-mm-dd.xls";

    my $path_dest = dirname($output);
    make_path($path_dest); 

    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;

    $response = $ua->get($url, ':content_file' => $output);
    print $response->headers->as_string,"\n";
    print $response->status_line, "\n";
    print 'saved to:', $output,"\n";

#    $url = 'https://www.chanet.com.cn/partner/pm_detail.cgi?pm_id=246';
#    print $url,"\n";

#    $output = '/tmp/chanet/comms/chanet246-1.html';
#    $path_dest = dirname($output);
#    make_path($path_dest); 
#
#    $response = $ua->get($url, ':content_file' => $output);
#    print $response->headers->as_string,"\n";
#    print $response->status_line, "\n";
#    print 'saved to:', $output,"\n";
#    print "\n";

}

sub parse_siters_2007 {
    my $output= "/tmp/chanet/promotio_linkYYYY-mm-dd.xlsx";
    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;
    if( not -f $output) {
        print "$output not a file \n";
        return ;
    }

    my $excel = Spreadsheet::XLSX -> new ($output);
    foreach my $sheet (@{$excel -> {Worksheet}}) {
        printf("Sheet: %s\n", $sheet->{Name});
        $sheet -> {MaxRow} ||= $sheet -> {MinRow};
        foreach my $row ($sheet -> {MinRow} .. $sheet -> {MaxRow}) {
            $sheet -> {MaxCol} ||= $sheet -> {MinCol};
            foreach my $col ($sheet -> {MinCol} ..  $sheet -> {MaxCol}) {
                my $cell = $sheet -> {Cells} [$row] [$col];
                if ($cell) {
                    printf("( %s , %s ) => %s\n", $row, $col, $cell -> {Val});
                }
            }
        }
    }
}

sub parse_siters {
    binmode(STDIN, ':encoding(utf8)');
    binmode(STDOUT, ':encoding(utf8)');
    binmode(STDERR, ':encoding(utf8)');

    my $output= "/tmp/chanet/promotio_linkYYYY-mm-dd.xls";

    my $path_dest = dirname($output);
    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;
    if( not -f $output) {
        print "$output not a file \n";
        return ;
    }

    my $parser   = Spreadsheet::ParseExcel->new();
    my $workbook = $parser->parse( $output );
    if ( !defined $workbook ) {
        die "Parsing error: ", $parser->error(), ".\n";
    }


    for my $worksheet ( $workbook->worksheets() ) {
        print "Worksheet name: ", $worksheet->get_name(), "\n\n";
        my ( $row_min, $row_max ) = $worksheet->row_range();
        my ( $col_min, $col_max ) = $worksheet->col_range();
        for my $row ( $row_min .. $row_max ) {
            for my $col ( $col_min .. $col_max ) {
                my $cell = $worksheet->get_cell( $row, $col );
                next unless $cell;
                print " ", $cell->value(),       "\t";
            }
            print "\n";
        }
    }
# insert into the chanet_advertisement;
    print "OK\n";
}

sub fetch_comms_html {
    my $config  = shift;
    my $date = localtime->strftime("%Y%m%d");
    my $output_tmpl  =  $config->{html_comm}->{output};
    $output_tmpl =~ s/YYYYmmdd/$date/;
    my $path_dest = dirname($output_tmpl);
    make_path($path_dest); 
    my $cookie_file = "/tmp/chanet/cookies_$date.txt";
    my $url_tmpl  = 'https://www.chanet.com.cn/partner/pm_detail.cgi?pm_id=%d';
    my $cookie_jar = HTTP::Cookies->new(
        'file' =>$cookie_file,
        'autosave'=>1,
        'ignore_depreacted'=> 0
    );
    my @ns_headers = (
        'Agent' =>'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:36.0) Gecko/20100101 Firefox/36.0',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' ,
#        'Accept-Encoding'=> 'gzip, deflate' ,
        'Accept-Language'=> 'en-US,en;q=0.5' ,
        'Host'=>'www.chanet.com.cn' 
    );
    my $cache = LWP::ConnCache->new();
    my $ua = LWP::UserAgent->new(
        'show_progress' => 1,
        'conn_cache'=> $cache,
    );
    $ua->protocols_allowed( [ 'http','https'] );
    $ua->timeout(120);
    $ua->cookie_jar($cookie_jar);
    my $ads_ids = fetch_ads_ids();
    my $i=0;
    for my $ads_id ( @$ads_ids) {
        #next if ($i++ == 1);
        my $output = sprintf( $output_tmpl, $ads_id);
        my $url =sprintf($url_tmpl, $ads_id);
        print $url,"\n";
        my $response = $ua->get($url,@ns_headers, ':content_file' => $output);
        print $response->headers->as_string,"\n";
        print  $response->status_line,"\n";
        print "Saved to ", $output,"\n";
    }
}

sub insert_chanet_advertiserment {
#    binmode(STDIN, ':encoding(utf8)');
#    binmode(STDOUT, ':encoding(utf8)');
#    binmode(STDERR, ':encoding(utf8)');

    my $config = shift;
    my $output = $config->{xls_sites}->{output};
    my $path_dest = dirname($output);

    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;

    if( not -f $output) {
        print "$output not found\n";
        return ;
    }

    print "  loading: ",$output ,"\n";

    my $path_curf = File::Spec->rel2abs(__FILE__);
    my ($vol, $dirs, $file) = File::Spec->splitpath($path_curf);
    my $parser = "java -Dfile.encoding=utf8 -jar $dirs/bin/xls2csv ";

    my $output_csv = $output;
    $output_csv =~ s/.xls$/.csv/;
    my $content = readpipe( $parser.' '.$output );

    # todo: 网址中包括了formula中的参数。
    $content =~ s/"&B[34]&"//g;
    open( my $fh , '+>', $output_csv)  or die "Could not open $output_csv $!\n";
    print $fh  $content;
    close($fh)|| warn "close failed: $!";


    print "  saved to ",$output_csv,"\n";
    # 读出转换好的 活动列表

    my $csv = Text::CSV->new ({auto_diag => 1, binary=>1,allow_whitespace=>1 })  # should set binary attribute.
        or die "Cannot use CSV: ".Text::CSV->error_diag ();
    open($fh, '<:encoding(utf8)', $output_csv) or die "Could not open '$output_csv' $!\n";

# 活动ID  活动名称    活动分类    链接类型    首页地址    推广链接   
# 340     东方CJ的CPS推广     商务/商店   首页推广链接    http://www.ocj.com.cn   0  
    my $i = 0;

    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth_create  =  $dbh->prepare( qq{INSERT INTO chanet_advertisement(  `ads_id`, `ads_name`, `category`, `ads_url_type`, `ads_url`, `marketing_url`, `fixed_hash`, `is_activated`) VALUES( ?,?,?,?,?,?,?,1) });

    my @ads_ids;
    while ( my $row = $csv->getline( $fh ) ) {

        next if (scalar @$row != 6);
        next if (length($row->[0]) == 0) ;
        next if ( $row->[0] !~ qr/^\d+$/ );

        print $i++,"<<<<\n";
        print '   ' , $row->[0], "\n";

        # ignore wap only url

        if ($row->[4] =~ m/:\/\/(wap|m)\./ ) {
            next;
        }

        # ignore duplicated 
        if( grep /^$row->[1]$/, @ads_ids) {
            next;
        }

        push @ads_ids , $row->[1]; 

        my $hash = calc_chanet_cps_advertisement_hash($row);
        my $ads_exist = query_chanet_advertiserment_by_fixed_hash($hash);

        if( defined($ads_exist) && $ads_exist->{is_activated} == 1) {
            next;
        } 

        my $sth_deactivate = $dbh->prepare(qq{ UPDATE chanet_advertisement SET is_activated = 0 WHERE ads_id = ? and is_activated = 1 });

        $sth_deactivate->execute(($row->[0]));
        $sth_deactivate->finish();

        if(! defined($ads_exist)) {
            # insert stuff
            push @$row,($hash); 
            my $rv = $sth_create->execute( values @$row);
            $sth_create->finish();
        } else {
            my $sth_activate = $dbh->prepare(qq{ UPDATE chanet_advertisement SET is_activated = 1 WHERE id  = ? limit 1});
            $sth_activate->execute(( $ads_exist->{id} ));
            $sth_activate->finish();
        }
        $dbh->commit;
    }
    $csv->eof or $csv->error_diag();
    close($fh)|| warn "close failed: $!";

# insert into the chanet_advertisement;
    print "OK\n";
}

sub calc_chanet_cps_advertisement_hash {
    my $row = shift;
    my $joined = join('-', @$row);
    utf8::encode($joined);
    my $hash = sha256_hex($joined); 
    return $hash;
}

sub query_chanet_advertiserment_by_fixed_hash {
    my $fixed_hash = shift;
    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, ads_id,is_activated FROM chanet_advertisement where fixed_hash = ? });
    $sth->execute( ($fixed_hash) );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref();
    return $hash_ref;
}

sub insert_commission {

    my $config = shift;

    my $file_reg = $config->{html_comm}->{output};
    my $path_dest = dirname($file_reg);

    my $date  = localtime->strftime("%Y%m%d");
    $file_reg =~  s/YYYYmmdd/$date/;
    $file_reg =~  s/\%d/(\\d\+)/;
    $file_reg = '^'.$file_reg.'$';

    binmode(STDIN, ':encoding(utf8)');
    binmode(STDERR, ':encoding(utf8)');

    opendir(my $dh, $path_dest) or  die $!;

    print $path_dest ,"\n";

    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $i = 0;
    my $ts = localtime->strftime("%Y-%m-%d %H:%M:%S");
    my $sth_comm = $dbh->prepare(qq{ INSERT INTO chanet_commission(ads_id, fixed_hash, is_activated, created_at) VAlUES (? , ?, 1, "$ts" ) } ) or die "Can't prepare : $dbh->errstr/n";
    my $sth_comm_data = $dbh->prepare(qq{ INSERT INTO chanet_commission_data(`commission_id`, `commission_serial_number`, `commission_name`, `commission`, `commission_period`, `description`, `created_at` ) VALUES ( \@chanet_commision_id , ? , ? ,? ,?,?, "$ts" ) }) or die "Can't prepare : $dbh->errstr/n";
    #读取.html文件
    while (my $file = readdir($dh)) {
        my $ads_id;
        print " >>>> seq : " ,$i,"\n";

        # file exists 
        if  ( ! -f "$path_dest/$file"){
            print "$path_dest/$file" ," NOT EXISTS\n";
            next;
        }

        # 文件名中有当天日期
        if( "$path_dest/$file" =~ m/$file_reg/) {
            $ads_id = $1;
        } else {
            print "$path_dest/$file" ," NOT MATCHED\n";
            next;
        }

        print  '>>>> ads_id : ', $ads_id ,"\n";

        my $parsed_html_hash_ref  = parse_html("$path_dest/$file", $ads_id);

        my $hash = $parsed_html_hash_ref->{comms_hash};
        my $comm_exist_ref =  query_chanet_commission_by_fixed_hash($hash, $ads_id );
        if( defined($comm_exist_ref) && $comm_exist_ref->{is_activated} == 1) {
            next;
        } 
        # deactivating the  current activated
        my $sth_deactivate = $dbh->prepare(qq{ UPDATE chanet_commission SET is_activated = 0 WHERE ads_id = ? and is_activated = 1 });
        $sth_deactivate->execute(($ads_id));
        $sth_deactivate->finish();

        if(! defined($comm_exist_ref) ) {
            my $comms_ref= $parsed_html_hash_ref->{comms_ref};
            $sth_comm->execute( ($ads_id , $hash ));
            $sth_comm->finish();
            my $sth_q = $dbh->prepare(qq{SELECT \@chanet_commision_id := id from chanet_commission WHERE fixed_hash = ? and ads_id = ? });
            $sth_q->execute(( $hash,$ads_id  ));
            foreach my $item(@$comms_ref) {
                $sth_comm_data->execute(@$item);
                $sth_comm_data->finish();
            }
        } else {
            # deactivating the  current activated
            my $sth_activate = $dbh->prepare(qq{ UPDATE chanet_commission SET is_activated = 1 WHERE id  = ? limit 1});
            $sth_activate->execute(( $comm_exist_ref->{id} ));
            $sth_activate->finish();
        }
        $dbh->commit;
        $i++;
    }
    closedir $dh;
}


sub parse_html {
    my $doc = shift; 
    my $ads_id = shift;
    open(my $fh, "<:utf8", $doc) || die;

    my $tree = HTML::TreeBuilder::XPath->new;
    $tree->parse_file($fh);

    my $xpath =  '/html/body/div[10]/div/div[1]/div[4]/div[1]/table[1]/tr';
    my @toc = $tree->findnodes($xpath);
    my $string ='';
    my @comms;
    my $i = 0;

    for my $el ( @toc ) {
        if ($i++ == 0) {
            next;
        }
        my @tds =  $el->findnodes('td');
        push @comms, [
            $tds[0]->as_trimmed_text, 
            $tds[1]->as_trimmed_text, 
            $tds[2]->as_trimmed_text, 
            $tds[3]->as_trimmed_text, 
            $tds[4]->as_trimmed_text,
        ];

        $string .= $tds[0]->as_trimmed_text.
        $tds[1]->as_trimmed_text.$tds[2]->as_trimmed_text.
        $tds[3]->as_trimmed_text.$tds[4]->as_trimmed_text;
    }

    close $fh;
    my $hash = calc_chanet_cps_commission_hash( $string.'-'.$ads_id );
    return { comms_ref =>\@comms , comms_hash => $hash };
}

sub calc_chanet_cps_commission_hash {
    my $joined = shift ;
    utf8::encode($joined); # compatible with php version ?
    my $hash = sha256_hex($joined); 
    return $hash;
}

# try to fetch from db
sub fetch_ads_ids {
    my @ads_ids;
    my $output= "/tmp/chanet/promotio_linkYYYY-mm-dd.xls";
    my $path_dest = dirname($output);
    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;
    if( not -f $output) {
        print "$output not a file \n";
        return ;
    }
    my $parser   = Spreadsheet::ParseExcel->new();
    my $workbook = $parser->parse( $output );
    if ( !defined $workbook ) {
        die "Parsing error: ", $parser->error(), ".\n";
    }
    for my $worksheet ( $workbook->worksheets() ) {
        my ( $row_min, $row_max ) = $worksheet->row_range();
        my ( $col_min, $col_max ) = $worksheet->col_range();
        for my $row ( $row_min .. $row_max ) {
            for my $col ( $col_min .. $col_max ) {
                my $cell = $worksheet->get_cell( $row, $col );
                next unless $cell;
                if( $row >= 6 && $col == 0  ) {
                    push @ads_ids , ( $cell->value() );
                }
            }
        }
    }
    @ads_ids = uniq @ads_ids;
    return \@ads_ids;
}

sub query_chanet_commission_by_fixed_hash {

    my $fixed_hash = shift;
    my $ads_id = shift;
    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, ads_id,is_activated FROM chanet_commission  where fixed_hash = ? and ads_id = ? });
    $sth->execute( ($fixed_hash, $ads_id ) );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref();
    return $hash_ref;
}

my $config = LoadFile( "./config/config.yml");

login();
##parse_siters();
##parse_siters_2007();
insert_chanet_advertiserment($config->{chanet});
#fetch_ads_ids();
##fetch_xls_sites( $config->{chanet} );
##captcha($config->{chanet} );
fetch_comms_html($config->{chanet});
insert_commission($config->{chanet});

__END__
https://www.chanet.com.cn/partner/get_all_links.cgi?pmid=0&search_type=1&search_as_id=480534&link_type=2&act-type=on&category=0
https://www.chanet.com.cn/partner/get_all_links.cgi?pmid=&search_type=2&search_as_id=480534&link_type=2&act-type=on&category=0

curl 'https://www.chanet.com.cn/partner/pm_detail.cgi?pm_id=246' 
-H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' 
-H 'Accept-Encoding: gzip, deflate' 
-H 'Accept-Language: en-US,en;q=0.5' 
-H 'Connection: keep-alive' 
-H 'Cookie: AdwaysCHANetpromotion15=1584%2C87909879176%2C1410847684%2C480534%2C135950%2C54%2C105%2C87909879176%2C1%26; AdwaysCHANetpromotion25=2503%2C88987882576%2C1413954878%2C480534%2C358559%2C45%2C105%2C88987882576%2C1%26; AdwaysCHANetpromotion6=645%2C91147264616%2C1419492915%2C480534%2C378636%2C49%2C105%2C91147264616%2C1%26661%2C92627949676%2C1424840886%2C480534%2C377023%2C76%2C%2C92627949676%2C30%26; AdwaysCHANetpromotion21=2171%2C89185536676%2C1414479370%2C480534%2C333073%2C51%2C105%2C89185536676%2C30%26; AdwaysCHANetpromotion5=533%2C79918401606%2C1398407894%2C480534%2C72294%2C47%2C1094111%2C79918401606%2C1%26514%2C89431487046%2C1415086904%2C480534%2C22822%2C48%2C1203235%2C89431487046%2C1%26; AdwaysCHANetpromotion13=1378%2C81225948496%2C1399957915%2C480534%2C359134%2C103%2C1094010%2C81225948496%2C30%261300%2C89177683866%2C1414464271%2C480534%2C351009%2C52%2C105%2C89177683866%2C1%26; AdwaysCHANetpromotion3=350%2C81833783606%2C1400815845%2C480534%2C216816%2C79%2C1057704%2C81833783606%2C1%26; AdwaysCHANetpromotion2=283%2C92380863246%2C1423822377%2C480534%2C9340%2C33%2C105%2C92380863246%2C1%26; AdwaysCHANetpromotion10=1053%2C92173243496%2C1423032393%2C480534%2C346379%2C88%2C1120386%2C92173243496%2C1%26; AdwaysCHANetpromotion24=2466%2C89175420486%2C1414459806%2C480534%2C370115%2C128%2C1120386%2C89175420486%2C30%26; AdwaysCHANetpromotion27=2743%2C91147319706%2C1419493026%2C480534%2C371059%2C118%2C105%2C91147319706%2C1%26; AdwaysCHANetpromotion26=2604%2C89175917676%2C1414460909%2C480534%2C337996%2C43%2C105%2C89175917676%2C30%26; AdwaysCHANetpromotion20=2046%2C89185533256%2C1414479362%2C480534%2C212334%2C53%2C105%2C89185533256%2C30%26; AdwaysCHANetpromotion7=773%2C91147259936%2C1419492906%2C480534%2C350282%2C123%2C105%2C91147259936%2C30%26; AdwaysCHANetpromotion22=2227%2C91147270216%2C1419492929%2C480534%2C375710%2C124%2C105%2C91147270216%2C30%26; Apache=127.0.0.1.1427464379047637; __utma=116180180.2005983665.1427464387.1427464387.1427866359.2; __utmz=116180180.1427464387.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ga=GA1.3.2005983665.1427464387; Hm_lvt_8a3ccf95e588f7c28f6f3595aa8d7ee2=1427464548,1427866351; Hm_lpvt_8a3ccf95e588f7c28f6f3595aa8d7ee2=1427867745; CGISESSID=5c96919e3f650ed8cdfc7044e9f7b826; __utmb=116180180.1.10.1427867740; __utmc=116180180; info=a8013cab6eec993c950cfc4b197f972275a728cb043b09ce22b73e21f6bef976voyage; _gat=1; __utmt=1' 
-H 'Host: www.chanet.com.cn' 
-H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:36.0) Gecko/20100101 Firefox/36.0'


perl -d:Modlist -S lwp-request -UusSeEda https://www.chanet.com.cn/partner/pm_detail.cgi?pm_id=246
