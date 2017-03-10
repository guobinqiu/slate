#!/usr/local/env perl
###########
# 
# Fri Mar 20 10:16:33 CST 2015
# 
# curl to fetch the csv file
# parse the csv file 
# insert into the table.
#
#########################

use warnings;
use strict;

use HTTP::Request;
use HTTP::Response;
use HTTP::Cookies;
use LWP::ConnCache;
use LWP::UserAgent;

use Text::CSV;
use utf8;
use Jili::DBConnection;

use Time::Piece;
use Digest::SHA qw(sha256_hex);
use Digest::MD5 qw(md5_hex);
use YAML::Syck;;
use File::Basename;
use File::Path qw(make_path);
use HTML::TreeBuilder::XPath;
use Data::Dumper;
use vars qw($database);

# login with account
# download the csv files.
sub fetch_duomai_cps_csv 
{
    my ( $config ) = @_;

    my $cookie_file = $config->{login}->{cookie_file};
    my $login_url = $config->{login}->{url};
    my $login_form_data = $config->{login}->{form_data};
    my $csv_sites_url = $config->{csv_sites}->{url};
    my $csv_sites_output=$config->{csv_sites}->{output};
    my $csv_sites_output_utf8 = $config->{csv_sites}->{output_utf8};;

    # 创建cookie file的目录
    my $path_dest = dirname($cookie_file) ;
    make_path($path_dest); 

    my $cookie_jar = HTTP::Cookies->new(
        file => $cookie_file ,
        autosave => 1,
    );

    my $ua = LWP::UserAgent->new();

    $ua->agent('Mozilla/4.73 [en] (X11; U; Linux 2.2.15 i686)');
    $ua->timeout(10);
    $ua->cookie_jar($cookie_jar);

    my $response = $ua->post( $login_url, $login_form_data );
    print $response->headers->as_string,"\n";

    $response = $ua->get($csv_sites_url ,':content_file' => $csv_sites_output );
    print $response->headers->as_string,"\n";

    die "download csv file failed " unless $response->status_line;

    # convert the encoding from gb2312 to utf8 
    open my $filter,'<:encoding(gb2312)',$csv_sites_output; 
    open my $filter_new, '+>:utf8',$csv_sites_output_utf8; 

    print $filter_new $_ while <$filter>; 

    close $filter  or die "$csv_sites_output\n";
    close $filter_new or die "$csv_sites_output_utf8\n";
} 


# parse the csv file

# the hash should remove the go.j5k6.com url in short url.
# it always changed alone without meaning.
sub calc_duomai_cps_advertisement_hash {
    my $row = shift;
    my $joined = join('-', @$row);

    utf8::encode($joined);
    my $hash = sha256_hex($joined); 
    return $hash;
}

# 返回商家的返利hash 
sub calc_duomai_cps_commission_hash 
{
    my $joined = shift ;
    utf8::encode($joined); # compatible with php version ?
    my $hash = sha256_hex($joined); 
    return $hash;
}

# 查询已经写入的商家属性hash
sub query_duomai_advertisement_by_fixed_hash 
{
    my $fixed_hash = shift;
    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, ads_id,is_activated FROM duomai_advertisement where fixed_hash = ? });
    $sth->execute( ($fixed_hash) );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref();
    return $hash_ref;
}


# 查询已经写入的商家返利的hash
sub query_duomai_commission_by_fixed_hash 
{
    my $fixed_hash = shift;
    my $ads_id = shift;
    
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, ads_id,is_activated FROM duomai_commission  where fixed_hash = ? and ads_id = ? });
    $sth->execute( ($fixed_hash, $ads_id ) );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref();
    return $hash_ref;
}

# insert into table
sub insert_duomai_advertisement {
    my ( $config ) = @_;
    my $file = $config->{csv_sites}->{output_utf8};
    print $file ,"\n";
    
    my $dbh = $database->{dbh};
    my $csv = Text::CSV->new ({auto_diag => 1, binary=>1,allow_whitespace=>1 })  # should set binary attribute.
        or die "Cannot use CSV: ".Text::CSV->error_diag ();

    open my $fh, "<:encoding(utf8)", $file or die "Could not open $file  $!\n";
    my $row = $csv->getline( $fh );
    my @title = @$row;

    # verify the exists hash
    # select ads_id  & is activated !  
    my $sql = qq{INSERT INTO duomai_advertisement(ads_id,ads_name,ads_url,ads_commission,start_time,end_time,category,return_day,billing_cycle,link_custom,fixed_hash,is_activated) VALUES ( ?,?,?,?,?,?,?,?,?,?,?,?)};

    while ( my $row1 = $csv->getline( $fh ) ) {
        next if (length($row1->[0])==0); # skip the 
        # remove the 网站id, 和,加密链接  

        my @row2 = @$row1[1..10];
        my $row = \@row2;

        my $hash = calc_duomai_cps_advertisement_hash($row);

        my $ads_exist = query_duomai_advertisement_by_fixed_hash($hash );

        if( defined($ads_exist) && $ads_exist->{is_activated} == 1) {
            next;
        } 

        my $sth_deactivate = $dbh->prepare(qq{ UPDATE duomai_advertisement SET is_activated = 0 WHERE ads_id = ? and is_activated = 1 });

        $sth_deactivate->execute(($row->[0]));
        $sth_deactivate->finish();

        if(! defined($ads_exist)) {
            # hash not exits
            push @$row,($hash ,1); 
            my $sth=$dbh->prepare($sql);
            my $rv = $sth->execute(  @$row);
            $sth->finish();
        } else {
            my $sth_activate = $dbh->prepare(qq{ UPDATE duomai_advertisement SET is_activated = 1 WHERE id  = ? limit 1});
            $sth_activate->execute(( $ads_exist->{id} ));
            $sth_activate->finish();
        }
        $dbh->commit;
    }

    # insert 
    $csv->eof or $csv->error_diag();
    close $fh or die "/tmp/a2.csv\n";
}

# 查询是否已经存在 相同的 商家属性hash
sub query_duomai_advertisement 
{
    # my $database = Jili::DBConnection->instance( );
    my $dbh = $database->{dbh};
    # now retrieve data from the table.
    my $sth = $dbh->prepare("SELECT * FROM duomai_advertisement where is_activated = 1");
    my @ads_ids ;
    $sth->execute();
    while (my $ref = $sth->fetchrow_hashref()) {
        push  @ads_ids, ( $ref->{'ads_id'});
    }
    $sth->finish();
    return @ads_ids;
}

# 下载商家返利页面
sub fetch_commission_csv 
{
    my $config = shift;
    my @ads_ids = query_duomai_advertisement();
    my $count_ids = @ads_ids;

    my $url_tmpl = $config->{html_comm}->{url};
    my $output_tmpl = $config->{html_comm}->{output};

    my $output_utf8_tmpl = $config->{html_comm}->{output_utf8};

    my $ds = localtime->strftime("%Y%m%d");
    $output_tmpl =~  s/YYYYmmdd/$ds/;

    my $path_dest = dirname($output_tmpl) ;
    make_path($path_dest); 

    my $cache = LWP::ConnCache->new(
        total_capacity=>$count_ids
    );

    my $cookie_jar = HTTP::Cookies->new(
        file => $config->{login}->{cookie_file}
    );

    my $ua = LWP::UserAgent->new(
        'agent'=>'Mozilla/4.73 [en] (X11; U; Linux 2.2.15 i686)',
        'timeout'=> 10,
        'cookie_jar'=> $cookie_jar,
        'conn_cache'=> $cache
    );

    my $i=0;
    my $response;
    my $url;
    my $fetched_file ;
    foreach my $ads_id ( @ads_ids  ) {
        $url = sprintf($url_tmpl, $ads_id);
        $fetched_file = sprintf($output_tmpl, $ads_id);
        $response = $ua->get($url,':content_file' => $fetched_file);
        print $response->headers->as_string,"\n";
        die "fetch comm html failed " unless $response->status_line;
    } 
    return ;
}


# parse the commissions .htmls

#'序号', '商品名称','佣金比例','有效期','备注');
sub parse_html 
{
    my $doc = shift; 
    my $ads_id = shift;

    my $tree = HTML::TreeBuilder::XPath->new;
    $tree->parse_file($doc);

    my $xpath = '/html/body/div[4]/div[3]/div[2]/table[1]/tr';
    my @toc = $tree->findnodes($xpath);

    my $string = $tree->findvalue($xpath);

    my $hash = calc_duomai_cps_commission_hash( $string.'-'.$ads_id );
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
    }
    return { comms_ref =>\@comms , comms_hash => $hash };
}

# insert comm 
sub insert_commission {
    my $config = shift; 
    my $file_reg = $config->{html_comm}->{output};
    my $path_dest = dirname($file_reg) ;
    my $ds = localtime->strftime("%Y%m%d");

    $file_reg =~  s/YYYYmmdd/$ds/;
    $file_reg =~  s/\%d/(\\d\+)/;
    $file_reg = '^'.$file_reg.'$';

    print $file_reg, "\n";
    opendir(my $dh, $path_dest) or  die $!;

    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $i = 0;
    my $ts = localtime->strftime("%Y-%m-%d %H:%M:%S");
    my $sth_comm = $dbh->prepare(qq{ INSERT INTO duomai_commission(ads_id, fixed_hash, is_activated, created_at) VAlUES (? , ?, 1, "$ts" ) } ) or die "Can't prepare : $dbh->errstr/n";
    my $sth_comm_data = $dbh->prepare(qq{ INSERT INTO duomai_commission_data(`commission_id`, `commission_serial_number`, `commission_name`, `commission`, `commission_period`, `description`, `created_at` ) VALUES ( \@duomai_commision_id , ? , ? ,? ,?,?, "$ts" ) }) or die "Can't prepare : $dbh->errstr/n";

    while (my $file = readdir($dh)) {
        my $ads_id;
        # Use a regular expression to find files ending in .txt
        if( "$path_dest/$file" =~ m/$file_reg/) {
            $ads_id = $1;
        } else {
            print "$path_dest/$file" ," NOT MATCHED\n";
            next;
        }
        # We only want files
        if  ( ! -f "$path_dest/$file"){
            print "$path_dest/$file" ," NOT EXISTS\n";
            next;
        }

        my $parsed_html_hash_ref  = parse_html("$path_dest/$file", $ads_id);
        my $hash = $parsed_html_hash_ref->{comms_hash};

        my $comm_exist_ref =  query_duomai_commission_by_fixed_hash($hash, $ads_id );

        if( defined($comm_exist_ref) && $comm_exist_ref->{is_activated} == 1) {
            next;
        } 

        my $sth_deactivate = $dbh->prepare(qq{ UPDATE duomai_commission SET is_activated = 0 WHERE ads_id = ? and is_activated = 1 });

        $sth_deactivate->execute(($ads_id));
        $sth_deactivate->finish();
        if(! defined( $comm_exist_ref)) {
            $sth_comm->execute( ($ads_id , $hash ));
            $sth_comm->finish();
            my $sth_q = $dbh->prepare(qq{SELECT \@duomai_commision_id := id from duomai_commission WHERE fixed_hash = ? and ads_id = ? });
            $sth_q->execute(( $hash,$ads_id  ));
            my $comms_ref= $parsed_html_hash_ref->{comms_ref};
            foreach my $item(@$comms_ref) {
                $sth_comm_data->execute(@$item);
                $sth_comm_data->finish();
            }
        } else {
            my $sth_activate = $dbh->prepare(qq{ UPDATE duomai_commission SET is_activated = 1 WHERE id  = ? limit 1});
            $sth_activate->execute(( $comm_exist_ref->{id} ));
        }
        $dbh->commit;
        $i++;
    }
    $dbh->commit;
    closedir $dh;
}


my $db_config = LoadFile( "./config/db.yml");
$database = Jili::DBConnection->instance(($db_config->{db}->{user},$db_config->{db}->{password},$db_config->{db}->{name},$db_config->{db}->{host}));

my $config = LoadFile( "./config/config.yml");

fetch_duomai_cps_csv( $config->{duomai});
insert_duomai_advertisement($config->{duomai});
fetch_commission_csv($config->{duomai});
insert_commission($config->{duomai});

__END__

活动ID,活动名称,网址,佣金,活动时间(起),活动时间(止),活动分类,RD,结算周期,自定义链接 
网站ID,活动ID,活动名称,网址,佣金,活动时间(起),活动时间(止),活动分类,RD,结算周期,自定义链接,加密链接 

select count(*) from duomai_advertisement;
select count(*) from duomai_commission;
select count(*) from duomai_commission_data;

truncate duomai_advertisement;
truncate duomai_commission;
truncate duomai_commission_data;
