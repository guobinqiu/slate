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
use Jili::DBConnection;

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
                print " $col:", $cell->value(),       "\t";
            }
            print "\n";
        }
    }
# insert into the chanet_advertisement;
    print "OK\n";
}

sub insert_emar_advertisement {
    my $config = shift;
# "/tmp/yiqifa/积粒网自定义链接全表YYYY-mm-dd.xls";
    my $output = $config->{xls_sites}->{output};
    my $ds = localtime->strftime("%Y-%m-%d");
    $output =~ s/YYYY-mm-dd/$ds/;

    if( not -f $output) {
        print "$output not a file \n";
        return ;
    }

    print "  loading: ",$output ,"\n";

    my $parser   = Spreadsheet::ParseExcel->new();
    my $workbook = $parser->parse( $output );
    if ( !defined $workbook ) {
        die "Parsing error: ", $parser->error(), ".\n";
    }

    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $sth_create  =  $dbh->prepare( qq{INSERT INTO emar_advertisement( `ads_id`,`ads_name`,`category`,`commission`,`commission_period`,`ads_url`,`can_customize_target`,`feedback_tag`,`marketing_url`,`fixed_hash`,`is_activated`) VALUES (?,?,?,?,?,?,?,?,?,?,1) });
    my $i=0; # insert counter
    my $u=0; # update counter
    for my $worksheet ( $workbook->worksheets() ) {
        my ( $row_min, $row_max ) = $worksheet->row_range();
        for my $row ( $row_min .. $row_max ) {
            if( $row == 0 ) {
                next;
            }

            # 从行中选择需要写入表的列
            my $fields = [
                $worksheet->get_cell( $row, 0 )->value(), # ads_id 
                $worksheet->get_cell( $row, 1 )->value(), # ads_name 
                $worksheet->get_cell( $row, 2 )->value(), #category            
                $worksheet->get_cell( $row, 3 )->value(),#commission          
                $worksheet->get_cell( $row, 4 )->value(),#commission_period   
                $worksheet->get_cell( $row, 8 )->value(),#ads_url             
                ( "yes" eq $worksheet->get_cell( $row, 9 )->value()) ? 1: 0 ,#can_customize_target
                $worksheet->get_cell( $row, 10)->value(),#feedback_tag 
                $worksheet->get_cell( $row, 11)->value(),#marketing_url       
                #fixed_hash          
                ##is_activated        
            ];

            # query by hash
            my $hash = calc_emar_cps_advertisement_hash($fields);
            my $ads_exist = query_emar_advertisement_by_fixed_hash($hash);
            if( defined($ads_exist) && $ads_exist->{is_activated} == 1 ) {

                if( $ads_exist->{marketing_url} ne $fields->[8] ) {
                    my $sth_activate = $dbh->prepare(qq{ UPDATE emar_advertisement SET  marketing_url = ?  WHERE id  = ? limit 1});
                    $sth_activate->execute(( $fields->[8], $ads_exist->{id} ));
                    $sth_activate->finish();
                    $u++;
                    $dbh->commit; 

print '    u:', $u,"\n";
print '    ',$_, "\n" for( @$fields);
print '    ', $hash,"\n";
print '    ', $fields->[8],"\n";
print '    ', $ads_exist->{marketing_url},"\n";

print "\n";
                }

                next;
            }

            my $sth_deactivate = $dbh->prepare(qq{ UPDATE emar_advertisement SET is_activated = 0 WHERE ads_id = ? and is_activated = 1 });
            $sth_deactivate->execute(($fields->[0]));
            $sth_deactivate->finish();

            if(! defined($ads_exist)) {
                #insert 
                push @$fields,($hash ); 
                #my $sth=$dbh->prepare($sql);
                my $rv = $sth_create->execute( values @$fields);
#                $sth_create->finish();
                $i++;
            } else {
                if( $ads_exist->{marketing_url} ne $fields->[8] ) {
                    my $sth_activate = $dbh->prepare(qq{ UPDATE emar_advertisement SET is_activated = 1 , marketing_url = ?  WHERE id  = ? limit 1});
                    $sth_activate->execute(( $fields->[8], $ads_exist->{id} ));
#                    $sth_activate->finish();
                    $u++;
print '    u:', $u,"\n";
print '    ',$_, "\n" for( @$fields);
print '    ', $hash,"\n";
print "\n";
                } else {
                    my $sth_activate = $dbh->prepare(qq{ UPDATE emar_advertisement SET is_activated = 1 WHERE id  = ? limit 1});
                    $sth_activate->execute((  $ads_exist->{id} ));
#                    $sth_activate->finish();
                }
            }
            $dbh->commit;
        }
    }

    print "  records update: $u\n";
    print "  records insert: $i\n";
}

sub query_emar_advertisement_by_fixed_hash {
    my $fixed_hash = shift;
    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, ads_id,is_activated , marketing_url FROM emar_advertisement where fixed_hash = ? });
    $sth->execute( ($fixed_hash) );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref();
    return $hash_ref;
}

# 防止写入相同的商家
sub query_emar_commission_by_fixed_hash {
    my $fixed_hash = shift;
    my $ads_id = shift;
    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, ads_id,is_activated FROM emar_commission  where fixed_hash = ? and ads_id = ? });
    $sth->execute( ($fixed_hash, $ads_id ) );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref();
    return $hash_ref;
}

# 批量下载商家返利详情的网页
sub fetch_comms_html {
    #todo verify the siters file history hash.
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

# 记录解析过的商家文件的hash,用于防止相同内容的文件重复解析
sub siter_file_hash {
    # open history file
    # get yesterday hash
    # get today hash
    my $hash_last='';
    my $hash_current='';
    if($hash_last eq $hash_current) {
        print "no changes on siters \n";
        return;
    }
}

# 序号,佣金类目,佣金,佣金周期,适用商品,详细说明,
sub parse_html {
    my $doc = shift; 
    my $ads_id = shift;
    my $tree = HTML::TreeBuilder::XPath->new;
    $tree->parse_file($doc);
    my $xpath = '/html/body/div/div/div[2]/div[8]/div/div[1]/table//tr';
    my @toc = $tree->findnodes($xpath);

    my $string = $tree->findvalue($xpath);
    my $hash = calc_emar_cps_commission_hash( $string.'-'.$ads_id );
    my @comms;

    my $i = 0;
    for my $el ( @toc ) {
        if ($i++ == 0) {
            next;
        }

        my @tds =  $el->findnodes('td');
        push @comms,  [ map { $_->as_trimmed_text}  @tds ]; 

#        print $_->as_trimmed_text,"\t"for( @tds);
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

# the hash should remove the go.j5k6.com url in short url.
# it always changed alone without meaning.
sub calc_emar_cps_advertisement_hash {
    my $row = shift;
    my $url = $row->[8]; 
    $row->[8] =~ s/k=.*&e=/k=&e=/;
    my $joined = join('-', @$row);
    $row->[8] = $url;

    utf8::encode($joined);
    my $hash = sha256_hex($joined); 
    return $hash;
}

sub calc_emar_cps_commission_hash {
    my $joined = shift ;
    utf8::encode($joined); # compatible with php version ?
    my $hash = sha256_hex($joined); 
    return $hash;
}

sub insert_commission{
    my $config = shift; 
    my $file_reg = $config->{html_comm}->{output_utf8};
    my $path_dest = dirname($file_reg) ;
    my $ds = localtime->strftime("%Y%m%d");

    $file_reg =~  s/YYYYmmdd/$ds/;
    $file_reg =~  s/\%d/(\\d\+)/;
    $file_reg = '^'.$file_reg.'$';

    opendir(my $dh, $path_dest) or  die $!;

    my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $i = 0;
    my $ts = localtime->strftime("%Y-%m-%d %H:%M:%S");
    my $sth_comm = $dbh->prepare(qq{ INSERT INTO emar_commission(ads_id, fixed_hash, is_activated, created_at) VAlUES (? , ?, 1, "$ts" ) } ) or die "Can't prepare : $dbh->errstr/n";
    my $sth_comm_data = $dbh->prepare(qq{ INSERT INTO emar_commission_data(`commission_id`,`commission_serial_number`,`commission_name`,`commission`,`commission_period`,`product_apply_to`,`description`,`created_at`) VALUES ( \@emar_commision_id , ? ,?, ? ,? ,?,?, "$ts" ) }) or die "Can`t prepare : $dbh->errstr/n";

        my $ads_id;
    while (my $file = readdir($dh)) {
        # Use a regular expression to find files ending in .txt
        if( "$path_dest/$file" =~ m/$file_reg/) {
            $ads_id = $1;
        } else {
            #  print "$path_dest/$file" ," NOT MATCHED\n";
            next;
        }

        # We only want files
        if  ( ! -f "$path_dest/$file"){
            #print "$path_dest/$file" ," NOT EXISTS\n";
            next;
        }
        print  '>>>> ads_id : ', $ads_id ,"\n";
        my $parsed_html_hash_ref  = parse_html("$path_dest/$file", $ads_id);
        my $hash = $parsed_html_hash_ref->{comms_hash};

        my $comm_exist_ref =  query_emar_commission_by_fixed_hash($hash, $ads_id );
        if( defined($comm_exist_ref) && $comm_exist_ref->{is_activated} == 1) {
            next;
        } 

        # do deactivate 
        my $sth_deactivate = $dbh->prepare(qq{ UPDATE emar_commission SET is_activated = 0 WHERE ads_id = ? and is_activated = 1 });

        $sth_deactivate->execute(($ads_id));
        $sth_deactivate->finish();
        
        if(! defined( $comm_exist_ref)) {
            # do insert new 
            $sth_comm->execute( ($ads_id , $hash ));
            $sth_comm->finish();
            my $sth_q = $dbh->prepare(qq{SELECT \@emar_commision_id := id from emar_commission WHERE fixed_hash = ? and ads_id = ? });
            $sth_q->execute(( $hash,$ads_id  ));
            my $comms_ref = $parsed_html_hash_ref->{comms_ref};
            foreach my $item(@$comms_ref) {
                $sth_comm_data->execute(@$item);
                $sth_comm_data->finish();
            }
        } else {
            # do active 
            my $sth_activate = $dbh->prepare(qq{ UPDATE duomai_commission SET is_activated = 1 WHERE id  = ? limit 1});
            $sth_activate->execute(( $comm_exist_ref->{id} ));
        }

        $dbh->commit;
    }
    $dbh->commit;
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

my $db_config = LoadFile( "./config/db.yml");
my $database = Jili::DBConnection->instance(($db_config->{user},$db_config->{password},$db_config->{name},$db_config->{host}));

my $config = LoadFile( "./config/config.yml");
# download comms html
# parse the comms html
# insert into database
login(); 
###parse_siters();
insert_emar_advertisement($config->{emar});
fetch_comms_html($config->{emar});
insert_commission($config->{emar});

__END__
 0:18046     1:第五大道移动CPS   2:奢侈品    3:1.4%-4.2%     4:xyd   5:纯url     8:第五大道WAP:http://m.5lux.com/    9:yes   10:c    11:http://p.yiqifa.com/n?k=2mLErnDS6E2OrI6H2mLErI6HWNzL6nMH6ljFWnzernt76NzOWQqD6n6Lg74HkQLErnb86Egy3ERlrIW-&e=c&t=http://m.5lux.com/   

