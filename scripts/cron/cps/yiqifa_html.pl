#!/usr/bin/perl

use warnings;
use strict;

use Data::Dumper;
use YAML::Syck;

use HTTP::Cookies::Netscape;
use LWP::ConnCache;
use LWP::UserAgent;
use HTML::TreeBuilder::XPath;

use Time::Piece;
use HTTP::Cookies;
use File::Basename;
use File::Path qw(make_path);

use Spreadsheet::ParseExcel;
use List::MoreUtils qw(uniq);
use Digest::SHA qw(sha256_hex);

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

    my $url_image = 'http://yiqifa.com/validateimage.do?d='.time();
print $url_image, "\n";

    my $res = $ua->get($url_image);
    if(!$res->is_success){
        return 0;
    }
    open(FILE_HANDLE,'>/tmp/img_yiqifa.jpg');
    binmode FILE_HANDLE;
    print FILE_HANDLE $res->content;
    close FILE_HANDLE;
}

sub login {
    my $url = 'http://yiqifa.com/userLogin.do';

    my $date = localtime->strftime("%Y%m%d");

    my $cookie_file = "/tmp/yiqifa/cookies_$date.txt";

    my $path_dest = dirname($cookie_file);
    make_path($path_dest); 

    my $cookie_jar = HTTP::Cookies->new(
        'file' => $cookie_file,
        'autosave'=>1,
        'ignore_discard'=> 1
    );

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

    my $url_image = 'http://yiqifa.com/validateimage.do?d='.time();
print $url_image, "\n";

    my $res = $ua->get($url_image);
    if(!$res->is_success){
        return 0;
    }
    open(FILE_HANDLE,'>/tmp/img_yiqifa.jpg');
    binmode FILE_HANDLE;
    print FILE_HANDLE $res->content;
    close FILE_HANDLE;

    print "input the /tmp/img_yiqifa.jpg: ","\n";
    my $captcha =  <STDIN>; 
    chomp ($captcha);    
    print $captcha,"\n";

    $response = $ua->post( $url, [
            'yhq'=> '',
            'userName'=> 'jili@voyagegroup.com.cn',
            'password'=> '123voyage',
            'loginFlag'=> '1',
            'checkCode'=> $captcha,
        ] );

    print $response->headers->as_string,"\n";
    die "download csv file failed " unless $response->status_line;

    if( $response->content =~ m/验证码错误，请重试/ ) {
        print "验证码错误，请重试\n";
        return ;
    }

    $url = 'http://yiqifa.com/earner/applyBatchLinkList.do?type=site';
    my $output= "/tmp/yiqifa/积粒网自定义链接全表YYYY-mm-dd.xls";
    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;

#    siteId=708089&linkType=URL&categoryd=0&schCampaignId=0&campaignName=
    $response = $ua->post( $url, [
            'siteId'=> '708089',
            'linkType'=> 'URL',
            'categoryd'=> '0',
            'schCampaignId'=> '0',
            'campaignName'=> '',
        ] );

    open(FILE_HANDLE,'>'. $output);
    binmode FILE_HANDLE;
    print FILE_HANDLE $response->content;
    close FILE_HANDLE;

    print $response->headers->as_string,"\n";
    print $response->status_line, "\n";
    print 'saved to:', $output,"\n";

}

# parse the xls

sub parse_siters {

    binmode(STDIN, ':encoding(utf8)');
    binmode(STDOUT, ':encoding(utf8)');
    binmode(STDERR, ':encoding(utf8)');

    my $output= "/tmp/yiqifa/积粒网自定义链接全表YYYY-mm-dd.xls";
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
    my $output_utf8_tmpl  =  $config->{html_comm}->{output_utf8};
    $output_tmpl =~ s/YYYYmmdd/$date/;
    $output_utf8_tmpl =~ s/YYYYmmdd/$date/;

    my $path_dest = dirname($output_tmpl);
    make_path($path_dest); 
    my $cookie_file = "/tmp/yiqifa/cookies_$date.txt";
    my $url_tmpl  = $config->{html_comm}->{url};

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
        'Host'=>'yiqifa.com' 
    );
    my $cache = LWP::ConnCache->new();
    my $ua = LWP::UserAgent->new(
        'show_progress' => 1,
        'conn_cache'=> $cache,
    );
    $ua->timeout(120);
    $ua->cookie_jar($cookie_jar);
    
    my $ads_ids = fetch_ads_ids();
    my $i=0;
    for my $ads_id ( @$ads_ids) {
        #next if ($i++ == 1);
        my $output = sprintf( $output_tmpl, $ads_id);
        my $output_utf8 = sprintf( $output_utf8_tmpl, $ads_id);
        my $url =sprintf($url_tmpl, $ads_id);

#print $output, "\n";
#print ' => ', $output_utf8, "\n";
#next;
        my $response = $ua->get($url,@ns_headers, ':content_file' => $output);
        open my $filter,'<:encoding(gbk)',$output; 
        open my $filter_new, '+>:utf8',$output_utf8; 
        print $filter_new $_ while <$filter>; 
        close $filter  or die "$output\n";
        close $filter_new or die "$output_utf8\n";

        print $response->headers->as_string,"\n";
        print  $response->status_line,"\n";
        print "Saved to ", $output_utf8,"\n";

    }
}

#'序号', '商品名称','佣金比例','有效期','备注');
sub parse_html {
    my $doc = shift; 
    my $ads_id = shift;

    my $tree = HTML::TreeBuilder::XPath->new;
    $tree->parse_file($doc);

    #my $xpath = '/html/body/div[4]/div[3]/div[2]/table[1]/tr';
    my $xpath = '/html/body/div/div/div[2]/div[8]/div/div[1]/table//tr';
    my @toc = $tree->findnodes($xpath);

    my $string = $tree->findvalue($xpath);

    my $hash = calc_yiqifa_cps_commission_hash( $string.'-'.$ads_id );
    print $hash, "\n";
    my @comms;

    my $i = 0;
    for my $el ( @toc ) {
        if ($i++ == 0) {
            next;
        }

        my @tds =  $el->findnodes('td');
print $tds[1]->as_trimmed_text;
#        push @comms, [
#            $tds[0]->as_trimmed_text, 
#            $tds[1]->as_trimmed_text, 
#            $tds[2]->as_trimmed_text, 
#            $tds[3]->as_trimmed_text, 
#            $tds[4]->as_trimmed_text,
#        ];
    }
    return { comms_ref =>\@comms , comms_hash => $hash };
}

sub calc_yiqifa_cps_commission_hash {
    my $joined = shift ;
    utf8::encode($joined); # compatible with php version ?
    my $hash = sha256_hex($joined); 
    return $hash;
}

sub insert_commission{
    my $config = shift; 
    my $file_reg = $config->{html_comm}->{output};
    my $path_dest = dirname($file_reg) ;
    my $ds = localtime->strftime("%Y%m%d");

    $file_reg =~  s/YYYYmmdd/$ds/;
    $file_reg =~  s/\%d/(\\d\+)/;
    $file_reg = '^'.$file_reg.'$';

    print $file_reg, "\n";
    opendir(my $dh, $path_dest) or  die $!;

    my $i = 0;

    while (my $file = readdir($dh)) {
        my $ads_id;
        print " >>>> seq : " ,$i,"\n";
        # Use a regular expression to find files ending in .txt
        if( "$path_dest/$file" =~ m/$file_reg/) {
            $ads_id = $1;
        } else {
            print "$path_dest/$file" ," NOT MATCHED\n";
            next;
        }
        print  '>>>> ads_id : ', $ads_id ,"\n";
        # We only want files
        if  ( ! -f "$path_dest/$file"){
            print "$path_dest/$file" ," NOT EXISTS\n";
            next;
        }

        my $parsed_html_hash_ref  = parse_html("$path_dest/$file", $ads_id);

        #my $hash = $parsed_html_hash_ref->{comms_hash};

        if ($i++ == 1) {
            exit;
        }
    }
    closedir $dh;
}

sub fetch_ads_ids {
    my @ads_ids;
    my $output= "/tmp/yiqifa/积粒网自定义链接全表YYYY-mm-dd.xls";
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
                if( $row >0 &&  $col == 0  ) {
                    push @ads_ids , ( $cell->value() );
                }
            }
        }
    }
    @ads_ids = uniq @ads_ids;
    return \@ads_ids;
}

my $config = LoadFile( "./config/config.yml");
# download comms html
# parse the comms html
# insert into database
# 
#login();
##parse_siters();
#insert_siters($config->{emar});
fetch_comms_html($config->{emar});
#insert_commission($config->{emar});

__END__

