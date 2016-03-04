#!/usr/bin/perl


use warnings;
use strict;

use Data::Dumper;

use List::MoreUtils qw(uniq);
use Jili::DBConnection;
use URI;
use Log::Log4perl;
use Time::Piece;

use YAML::Syck;;

use File::Basename;
use File::Path qw(make_path);

use HTML::TreeBuilder::XPath;
use LWP::Simple;  

use Digest::MD5 qw(md5_hex);

use File::Type;
use File::Copy;
use vars qw($database);

# merge into cps_advertisement
# select from aps 
# check exists ? 

# loop
#   {chanet,emar,duomai}.url with tidy
#   {chanet,emar,duomai}.title
    
# 取 cps的 ad_category.id;
sub query_ad_category_id {
    my ($asp,$category_name) = @_;

    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id FROM ad_category where asp = ?  and  category_name = ?});
    $sth->execute(( $asp, $category_name)  );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref(); 
    if ( defined( $hash_ref->{id}) ) {
        return $hash_ref->{id};
    }
    return 0;
}


# chanet cps
# $VAR1 = {
#           'marketing_url' => 'http://count.chanet.com.cn/click.cgi?a=480534&d=383449&u=&e=&url=http%3A%2F%2Fwww.supuy.com%2F',
#           'ads_id' => '2939',
#           'id' => '108',
#           'ads_name' => '速普商城CPS推广',
#           'ads_url' => 'http://www.supuy.com/'
#         };
sub push_chanet_advertisement {
    my ($ads_cat_hashref ) = @_;
 # read records
    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, ads_id, ads_name,category,ads_url,marketing_url,selected_at FROM chanet_advertisement where is_activated = 1});
    $sth->execute( );
    $dbh->commit;
    my $ad_category_id = $ads_cat_hashref->{chanet_advertisement};
    my $logger = Log::Log4perl->get_logger('logger.cps.debug');

    #insert statement 
    my $i=0;
    my $j = 0;
    while( my $hash_ref = $sth->fetchrow_hashref()) {
       $i++; 
        # do filter
        my $ads_url = $hash_ref->{ads_url};
        chomp($ads_url);
        # 空的ads_url
        if(length($ads_url ) == 0) {
            #print "[CPS][CHANET][FILTER][WARN] empty ads_url\n";
            next;
        } 
        # 没有http://的ads_url
        if( $ads_url !~ m/^https?:\/\// ) {
            $ads_url = 'http://'.$ads_url;
        }

        # 取domain/host
        my $uri = URI->new( $ads_url);
        my $web_domain = $uri->host;
        my $ads_name = $hash_ref->{ads_name};

        # filter the title 
        $ads_name =~ s/[\s+]//g;
        $ads_name =~ s/(（含wap）|（返利站）|（返利类）|（展示类）)$//ig;
        $ads_name =~ s/(CPS效果营销|ROI推广|的CPS推广|CPS推广活动|CPS活动推广|CPS活动|广活动|CPS推广|推广CPS|CPS)$//ig;

        my $cps_params = {
            ad_category_id =>$ad_category_id,
            ad_id =>$hash_ref->{id},
            title=>$ads_name,
            marketing_url=>$hash_ref->{marketing_url},
            ads_url=>$hash_ref->{ads_url},
            website_name =>$ads_name,
            website_host =>$web_domain,
            website_category =>$hash_ref->{category},
            selected_at =>$hash_ref->{selected_at},
            is_activated =>0 #  is_activated
        };

        insert_cps_advertisement($cps_params);
        $j++;
    }
    #print "[CPS][CHANET][INFO] j: $j   \n";
} 

sub push_duomai_advertisement {
    # read records
    my ($ads_cat_hashref ) = @_;
    # read records
    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $sth=$dbh->prepare(qq{SELECT `id`,`ads_id`,`ads_name`,`ads_url`,`ads_commission`,`start_time`,`end_time`,`category`,`return_day`,`billing_cycle`,`link_custom`,`selected_at` FROM duomai_advertisement where is_activated = 1});
    $sth->execute();
    $dbh->commit;

    my $ad_category_id = $ads_cat_hashref->{duomai_advertisement};
    my $logger = Log::Log4perl->get_logger('logger.cps.debug');

    my $i=0;
    my $j=0;

    while( my $hash_ref = $sth->fetchrow_hashref()) {
        $i++; 
        my $ads_url = $hash_ref->{ads_url};
        chomp($ads_url);

        # 空的ads_url
        if(length($ads_url ) == 0) {
            #print "[CPS][DUOMAI][FILETER][WARN] empty ads_url\n";
            next;
        } 
        # 没有http://的ads_url
        if( $ads_url !~ m/^https?:\/\// ) {
            $ads_url = 'http://'.$ads_url;
        }

        # 取domain/host
        my $uri = URI->new( $ads_url);
        my $web_domain = $uri->host;
        if( $web_domain =~ m/^(m|wap)\./ ) {
            #print "[CPS][DUOMAI][FILETER][WARN] mob/wap ads_url\n";
            next;
        }

        # filter by the title 
        my $ads_name = $hash_ref->{ads_name};
        if ( $ads_name =~ m/(wap端|wap|移动端)/ ) {
            #print "[CPS][DUOMAI][FILTER][WARN] mob/wap ads_name\n";
            next;
        }
        $ads_name =~ s/[\s+]//g;
        $ads_name =~ s/(CPS推广|返点站推广|高佣金CPS)$//ig;
        my $cps_params = {
            ad_category_id => $ad_category_id, # ad_category_id
            ad_id=>$hash_ref->{id}, # ad_id ;
            title=>$ads_name, # title ;
            marketing_url=> $hash_ref->{link_custom}, # marketing_url;
            ads_url => $hash_ref->{ads_url}, #ads_url, web_host;
            website_name=>$ads_name, #  website_name ;
            website_host=>$web_domain, #website_host
            website_category=>$hash_ref->{category}, #website_category
            selected_at=>$hash_ref->{selected_at}, 
            is_activated=>0   
        };
        insert_cps_advertisement($cps_params);
        $j++;
    } #eof while
    #print "[CPS][DUOMAI][INFO] j: $j\n";
} #eof duomai 

sub push_emar_advertisement {
    # read records
    my ($ads_cat_hashref ) = @_;
    # read records
    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $sth=$dbh->prepare(qq{SELECT `id`,`ads_id`,`ads_name`,`category`,`commission`,`commission_period`,`ads_url`,`can_customize_target`,`feedback_tag`,`marketing_url`,`selected_at` FROM emar_advertisement where is_activated = 1});
    $sth->execute();
    $dbh->commit;

    my $ad_category_id = $ads_cat_hashref->{emar_advertisement};
    my $logger = Log::Log4perl->get_logger('logger.cps.debug');

    my $i=0;
    my $j=0;

    while( my $hash_ref = $sth->fetchrow_hashref()) {
        $i++; 

        # 取domain/host
        my $ads_url = $hash_ref->{ads_url};
        chomp($ads_url);
        if( $ads_url =~ m/.*https?.*/ ) {
            $ads_url =~ s/^.*(https?:\/\/)/$1/;
        } elsif ( $ads_url =~ m/.*：([a-zA-Z0-9].*)$/ ) {
            $ads_url =~ s/.*：([a-zA-Z0-9].*)$/$1/;
            $ads_url = 'http://'.$ads_url;
        } else {
            #print "[CPS][EMAR][FILETER][WARN] invalid $ads_url\n";
        }
        my $uri = URI->new( $ads_url);
        my $web_domain = $uri->host;

        chomp($web_domain);
        if( $web_domain =~ m/^(m|wap)\./ ) {
            #print "[CPS][EMAR][FILETER][WARN] mob/wap $ads_url\n";
            next;
        }

        my $web= $hash_ref->{ads_url};
        my $ads_name = $hash_ref->{ads_name};
        $ads_name =~ s/\s+//g;
        if ( $ads_name =~ m/(移动CPS)$/i ) {
            #print "[CPS][DUOMAI][FILTER][WARN] mob/wap $ads_name\n";
            next;
        }
        $ads_name =~ s/(高佣金CPS|roi|CPS)$//ig;
        $ads_name =~ s/CPS(\(天猫店\)|\(淘宝店\))$/$1/ig;


        my $cps_params = {
            ad_category_id => $ad_category_id, # ad_category_id
            ad_id=>$hash_ref->{id}, # ad_id ;
            title=>$ads_name, # title ;
            marketing_url=> $hash_ref->{marketing_url}, # marketing_url;
            ads_url => $ads_url, #ads_url, web_host;
            website_name=>$ads_name, #  website_name ;
            website_host=>$web_domain, #website_host
            website_category=>$hash_ref->{category}, #website_category
            selected_at=>$hash_ref->{selected_at}, 
            is_activated=>0   
        };

        insert_cps_advertisement($cps_params);
        $j++;
    } 
    #print "[CPS][emar][INFO] j: $j\n";
# return rows inesert & replace 
} 

sub insert_cps_advertisement {
    my ($enter_fields ) = @_;

    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    #insert statement 
    my $sth_insert = $dbh->prepare(qq{ INSERT INTO cps_advertisement(`ad_category_id`, `ad_id`, `title`, `marketing_url`, `ads_url`,  `website_name`,`website_host`,`website_category`, `selected_at`,`is_activated` ) VALUES ( ?,?,?, ?,?,?, ?,?,?, ?  ) }) or die "Can't prepare : $dbh->errstr/n";

    my $sth_update = $dbh->prepare(qq{ UPDATE cps_advertisement SET `ad_category_id`=?,`ad_id`=?,`title`=?,`marketing_url`=?,`ads_url`=?,`website_name`=?,`website_category`=?,`selected_at`=?,`is_activated`=? WHERE id = ? AND `website_host`=? limit 1 }) or die "Can't prepare : $dbh->errstr/n";

    my $exists_hashref = search_exists_by_webdomain( $enter_fields->{website_host});           
    #do add , 当前为空 或只有1条状态为使用中(is_actvaited == 1)。
    if( ! defined($exists_hashref) || $exists_hashref->{is_activated} == 1) {
        # insert new !
        my $cps_params = [
            $enter_fields->{ad_category_id},
            $enter_fields->{ad_id}, #$hash_ref->{id}, # ad_id ;
            $enter_fields->{title},
            $enter_fields->{marketing_url},
            $enter_fields->{ads_url},
            $enter_fields->{website_name},
            $enter_fields->{website_host},
            $enter_fields->{website_category},
            $enter_fields->{selected_at},
            0 
        ];
        $sth_insert->execute(@$cps_params);
        $dbh->commit();
        return 0; 
    }

    #go next, 如果己存在的记录为is_activated == 0 && selected_at = undef 
    if( ! defined( $exists_hashref->{selected_at} )  ) {
        #print "[CPS][PUSH][INFO] exists $enter_fields->{website_host} win(unused)\n";
        return 0; 
    }

    #go next, 如果己存在的记录为 is_activated == 0 &&  
    #         exists.selected_at   < enter.selected_at(早于enter记录)
    if( defined($enter_fields->{selected_at}) ) {
        my $t_exists = datetime_str_to_int($exists_hashref->{selected_at});
        my $t_enter  = datetime_str_to_int($enter_fields->{selected_at});

        if( $t_exists <  $t_enter) {
            #print "[CPS][PUSH][INFO] exists $enter_fields->{website_host} win(earlier)\n";
            return 0; 
        }
    } 

    # do  update
    #print "[CPS][PUSH][INFO] exists $enter_fields->{website_host} win(... to update)\n";
    my $cps_params = [
        $enter_fields->{ad_category_id},
        $enter_fields->{ad_id},
        $enter_fields->{title},
        $enter_fields->{marketing_url},
        $enter_fields->{ads_url},
        $enter_fields->{website_name},
        $enter_fields->{website_category},
        $enter_fields->{seleted_at},
        $enter_fields->{is_activated},
        $exists_hashref->{id},
        $enter_fields->{website_host},
    ];
    $sth_update->execute(@$cps_params);
    $dbh->commit();
}

# 查出已经存在的商家活动 ,如果有is_activated = 1,0的2个记录，只返回is_activated==0的。
sub search_exists_by_webdomain{
    my ($web_domain) = @_;
    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};
    my $sth=$dbh->prepare(qq{SELECT id, selected_at,is_activated  FROM cps_advertisement where website_host = ? order by is_activated asc limit 1});
    $sth->execute(( $web_domain )  );
    $dbh->commit;
    my $hash_ref = $sth->fetchrow_hashref(); 
    if ( defined( $hash_ref) ) {
        return $hash_ref;
    }
    return ;
}

# status: 0:写入时 -> 1:使用中 -> 2:弃用中
# 修改 selected_at 
# 将当前表中的 使用中的商家活动 删除，
# 将写入的商家活动转为活跃。
sub do_activate {
    my ($ads_cat_hashref) = @_;
    # status 1->2 
    my $ts = localtime->strftime("%Y-%m-%d %H:%M:%S");
    # my $database = Jili::DBConnection->instance();
    my $dbh = $database->{dbh};

    my $sth_deactivate = $dbh->prepare(qq{UPDATE cps_advertisement SET is_activated = ? WHERE is_activated = ? });
    $sth_deactivate->execute((2,1));
    print "|||rows deactive(1->2): ", $sth_deactivate->rows,"\n";

    # status 0->1 
    my $sth_activate =$dbh->prepare(qq{UPDATE cps_advertisement SET is_activated = ?, selected_at = ?  where is_activated = ?});
    $sth_activate->execute((1,$ts, 0));
    print "|||rows activated(0->1): ", $sth_activate->rows,"\n";

    # update with join  select 

    for my $ads_table ( keys %$ads_cat_hashref) {
        my $sth_upate_selected_at = $dbh->prepare(qq{UPDATE $ads_table AS a INNER JOIN cps_advertisement AS b ON a.id = b.ad_id SET a.selected_at = b.selected_at WHERE b.ad_category_id = ?  AND b.is_activated = 1 });
        $sth_upate_selected_at->execute(( $ads_cat_hashref->{$ads_table}  ));
        print "|||rows updated $ads_table.seleted_at: ", $sth_upate_selected_at->rows,"\n";
    }

    # chanet ,
    my $sql_delete_deprecated = $dbh->prepare( qq{delete from cps_advertisement where is_activated = 2 limit ? });
    $sql_delete_deprecated->execute( ($sth_deactivate->rows) );

    print "|||rows deleted (is_activated==2): ",$sql_delete_deprecated->rows,"\n" ;

    my $sql_update = qq{update cps_advertisement SET website_name_dictionary_key = IF( ascii(website_name) < 128,  LEFT(website_name,1), ELT( INTERVAL( CONV( HEX( left( CONVERT( website_name USING gbk ) , 1 ) ) , 16, 10 ) , 0xB0A1, 0xB0C5, 0xB2C1, 0xB4EE, 0xB6EA, 0xB7A2, 0xB8C1, 0xB9FE, 0xBBF7, 0xBFA6, 0xC0AC, 0xC2E8, 0xC4C3, 0xC5B6, 0xC5BE, 0xC6DA, 0xC8BB, 0xC8F6, 0xCBFA, 0xCDDA, 0xCEF4, 0xD1B9, 0xD4D1 ) , 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z' ))};

    my $sth_update= $dbh->prepare($sql_update );
    $sth_update->execute();

    print "|||rows website_name_dictionary_index(updated): ",$sth_update->rows,"\n" ;

    $dbh->commit;
    # delete status == 2 
}

# 2015-10-11 00:00:00 -> int
sub datetime_str_to_int{
    my ($s) = @_;
    my $t = Time::Piece->strptime($s, "%Y-%m-%d %H:%M:%S");
    return $t->epoch;
}

#    logo_path: "/tmp/yiqifa/logo/"
# read emar comm file list
# parse 
# download 

# query cps_advertisement to confirm all website has the logo file
# log those websites_domain which  not has log   into the  {logo_file}
sub logo_update 
{
    my ($config) = @_;
    # prepare the path
    my $logo_path = $config->{logo_path}; 
    make_path($logo_path); 

    # looking for the detail page, to fetch the logo uri
    my $ds = localtime->strftime("%Y%m%d");
    my $file_reg = $config->{html_comm}->{output_utf8};
    $file_reg =~  s/YYYYmmdd/$ds/;
    $file_reg =~  s/\%d/(\\d\+)/;
    $file_reg = '^'.$file_reg.'$';

    my $path_dest = $config->{html_comm}->{output_dir} ; 
    opendir(my $dh, $path_dest) or  die $!;

    my $i=0;
    my $ads_id;
    while (my $file = readdir($dh)) {
        $i++;
        # look for the shop detail page 
        if( "$path_dest/$file" =~ m/$file_reg/) {
            $ads_id = $1;
        } else {
            next;
        }

        #  not found  shop detail html. 
        if  ( ! -f "$path_dest/$file"){
            print  STDERR   "Not found $path_dest/$file\n";
            next;
        }

        my $tree = HTML::TreeBuilder::XPath->new;
        $tree->parse_file("$path_dest/$file");

        # xpath  domain ( yiqifa) 
        my $web_url_xpath = '/html/body/div/div/div[2]/div[2]/div/table/tr[2]/td[2]/a';
        my @el_ = $tree->findnodes($web_url_xpath );
        my $url =   $el_[0]->as_trimmed_text;
        my $domain = $url;
        $domain =~ s/http:\/\/([a-zA-Z\.\-])/$1/;

        # xpath to fetch logo uri (yiqifa)
        my  $logo_xpath = '/html/body/div/div/div[2]/div[2]/div/table/tr[1]/td[1]/img';
        my @el = $tree->findnodes($logo_xpath );
        my $uri_logo =  $el[0]->attr('src');

        my $logo_name = md5_hex($domain);
        # skip if exits 
        if(  -f  $logo_path.'/'.$logo_name){
            next;
        }

        # do downloading 
        my $res = get($uri_logo);
        open(FILE_HANDLE,'>'. $logo_path.'/' . $logo_name);
        binmode FILE_HANDLE;
        print FILE_HANDLE $res;
        close FILE_HANDLE;
        closedir $dh;
    }
}

# query, check: {x |cps_advertisement.website_url -  exits }
sub logo_check 
{
    my ($config, $ads_cat_hashref) = @_;

    make_path($config->{duomai}->{logo_path}); 
    make_path($config->{chanet}->{logo_path}); 

    my $dbh = $database->{dbh};
    my $sth = $dbh->prepare(qq{SELECT ad_category_id,ad_id, website_host FROM cps_advertisement WHERE is_activated = 1});

    $sth->execute();
    $dbh->commit();

    while ( my $cps_hash_ref = $sth->fetchrow_hashref() ) {
        fetchLogo($config ,$ads_cat_hashref,  $cps_hash_ref  );
    }
}

# parse the detail page for logo uri( duomai & chanet)
sub fetchLogo 
{
    my ($config, $ads_cat_hashref, $cps_hash_ref)   = @_;

    # return if exits
    my $domain = $cps_hash_ref->{website_host};
    my $logo_name = md5_hex($domain);

    my $yiqifa_logo_path = $config->{emar}->{logo_path}; 

    if(  -f $yiqifa_logo_path. $logo_name){
        return 0;
    }

    my $ad_cat_id = $cps_hash_ref->{ad_category_id};

    # get the aps name
    my $cps_tbl_name = '';
    foreach my $k ( keys %$ads_cat_hashref) {
        if( $ad_cat_id eq $ads_cat_hashref->{$k} ) {
            $cps_tbl_name = $k ;
            last;
        }
    }
    if( length($cps_tbl_name) == 0 ) {
        return 1;
    }

    my $tag_in_config = $cps_tbl_name;
    $tag_in_config =~ s/_advertisement//;

    my $save_to = $config->{$tag_in_config}->{logo_path};
    my $logo_file = $save_to. $logo_name;
    # return if log file already exits
    if( -f $logo_file ) {
        return 0;
    }

    # query responding table  for ads_id , the build the detail page name
    my $dbh = $database->{dbh};
    my $sth = $dbh->prepare(qq{SELECT ads_id FROM $cps_tbl_name WHERE id = ? limit 1 });
    #select website_host from cps_advertisement where is_activated = 1
    $sth->execute(( $cps_hash_ref->{ad_id}) );
    $dbh->commit();
    my $asp_hash_ref = $sth->fetchrow_hashref(); 

    if(! defined($asp_hash_ref)) {
        return 2;;
    } 

    # looking for the  detail page
    my $detail_file ;
    if( defined( $config->{$tag_in_config}->{html_comm}->{output_utf8})) {
        $detail_file = $config->{$tag_in_config}->{html_comm}->{output_utf8};
    } elsif(defined( $config->{$tag_in_config}->{html_comm}->{output} )) {
        $detail_file = $config->{$tag_in_config}->{html_comm}->{output};
    } else {
        return 3; # no detail page
    }


    my $ds = localtime->strftime("%Y%m%d");
    $detail_file =~ s/YYYYmmdd/$ds/;
    $detail_file =~ s/%d/$asp_hash_ref->{ads_id}/;

    # parse the detail page for logo uri

    if( ! -f $detail_file) 
    {
        print  STDERR   'Not found ',$detail_file,"\n";
        return 4;
    }

    my $xpath_logo =  $config->{$tag_in_config}->{html_comm}->{logo_xpath};
    my $tree = HTML::TreeBuilder::XPath->new;
    $tree->parse_file($detail_file);
    my @el = $tree->findnodes($xpath_logo );
    my $uri_logo =  $el[0]->attr('src');


    if( not  $uri_logo =~ m/^http/) {
        $uri_logo = 'http://'. $config->{$tag_in_config}->{host}.$uri_logo;
    }


    # download it !
    # do download 
    my $res = get($uri_logo);
    open(FILE_HANDLE,'>'. $logo_file);
    binmode FILE_HANDLE;
    print FILE_HANDLE $res;
    close FILE_HANDLE;

}

#  add file extension to the image file
sub logo_rename 
{
    my ($config) = @_;
    my @paths_logo = (
        $config->{chanet}->{logo_path},
        $config->{duomai}->{logo_path},
        $config->{emar}->{logo_path}
    );

    my $ft = File::Type->new();
    foreach my $path ( @paths_logo) {
        opendir(my $dh, $path) or  die $!;

        while (my $filename  = readdir($dh)) {
            my $logo_file=  $path . $filename;
            if(not  -f $logo_file) {
                next;
            }
            # only md5 has file name will be copying
            if(not $logo_file =~ m/.*\/[a-f0-9]{32}$/ ) {
                next
            }

            my  $suffix = '';
            my $type_1 = $ft->mime_type($logo_file);
            if ($type_1 eq 'image/jpeg') {
                $suffix = 'jpg';
            } elsif ($type_1 eq 'image/gif') {
                $suffix = 'gif';
            } elsif ($type_1 eq 'image/x-png') {
                $suffix = 'png';
            } else {
                next;
            }

            if (length($suffix) == 0) {
                next;
            }

            # target file name already exists 
            if( -f $logo_file.'.'.$suffix ) {
                next;
            }

            copy($logo_file ,$logo_file.'.'.$suffix) or die "Copy failed: $!";
        } 
    }
}

# 生成图片的文件名。
sub calcLogFileNameByDomain
{
    my($domain) = @_;
    return md5_hex($domain);
}

my $db_config = LoadFile( "./config/db.yml");
$database = Jili::DBConnection->instance(($db_config->{db}->{user},$db_config->{db}->{password},$db_config->{db}->{name},$db_config->{db}->{host}));

Log::Log4perl::init('config/log4perl.conf');

my $ads_cat_hashref  = {
    chanet_advertisement => query_ad_category_id( 'chanet', 'cps'), 
    emar_advertisement   => query_ad_category_id( 'emar', 'cps'), 
    duomai_advertisement => query_ad_category_id( 'duomai', 'cps'), 
};


## 将各cps写入表中
push_emar_advertisement($ads_cat_hashref);
push_chanet_advertisement($ads_cat_hashref);
push_duomai_advertisement($ads_cat_hashref );

### 完成新旧cps的转换
do_activate($ads_cat_hashref);

my $config = LoadFile( "./config/config.yml");

#TODO: list those new  donwloaded logo files 
logo_update($config->{emar});
logo_check($config, $ads_cat_hashref);
logo_rename($config);


__END__

select count(*) from cps_advertisement;
truncate cps_advertisement;

#1. 将.gif .png转为jpg
#2. 与目前在使用中的 .jpg合并

mkdir /tmp/logos
rm -rf /tmp/logos
bash convert_batch.sh   /data/91jili/cps/chanet/logo /tmp/logos
bash convert_batch.sh   /data/91jili/cps/duomai/logo /tmp/logos

cd  /tmp/logos
find /data/91jili/cps/*/logo/*.jpg -exec cp {} \;

cp -rf /var/www/html/jili/web/images/website_logos/*  /tmp/logos 
cp -rf  /tmp/logos/* /var/www/html/jili/web/images/website_logos/





