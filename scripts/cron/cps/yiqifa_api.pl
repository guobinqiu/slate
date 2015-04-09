#!/usr/local/env perl

use warnings;
use strict;

use HTTP::Request;
use HTTP::Response;
use HTTP::Cookies;
use LWP::ConnCache;
use LWP::UserAgent;

use YAML::Syck;;
use Jili::Yiqifa::WebsiteCategoryGetRequest;
use Jili::Yiqifa::WebsiteListGetRequest;
use Jili::Yiqifa::YiqifaOpen;  
use Jili::Yiqifa::YiqifaUtils;  
use utf8;
use Encode;
use Data::Dumper;

#binmode(STDIN, ':encoding(utf8)');
binmode(STDOUT, ':encoding(utf8)');
#binmode(STDERR, ':encoding(utf8)');


# api ? 
# not commission

sub fetch_api {
    my $config  = shift;
    print $config->{xls_sites}->{url},"\n";
}


my $config = LoadFile( "./config/config.yml");

fetch_api($config->{emar});
my $yiqifa_urils=   Jili::Yiqifa::YiqifaUtils->new;
print $yiqifa_urils->{BASE_URL},"\n";
#    emar_com.demo.AppKey: '139597428026718017'
#//    emar_com.demo.AppSecret: 'e7c5367e1ba7d609865389640007df3e'


my $c = Jili::Yiqifa::YiqifaOpen->new({ consumerKey=>"139597428026718017",consumerSecret=> "e7c5367e1ba7d609865389640007df3e"});
$c->format( 'json');

sub update_web_category {
    my $req = Jili::Yiqifa::WebsiteCategoryGetRequest->new;
    $req->setFields('web_catid,web_cname,web_type');
    $req->setWtype(1);
    my $resp =$c->execute($req);
    if( 'HASH' eq ref($resp) && defined($resp->{web_cats})) {
        my $web_cats = $resp->{web_cats}->{web_cat};
        my $i = 0;
        for (@$web_cats) {
            print $i++, ":\n";
            print ' web_catid: ',$_->{web_catid},"\n"; 
            print ' web_cname: ',$_->{web_cname},"\n"; 
            print ' web_type:  ',$_->{web_type},"\n"; 

            #web_catid":"1","web_cname":"综合商城","web_type":"1
        }
    } 
}

my $req = Jili::Yiqifa::WebsiteListGetRequest->new;
$req->setFields("web_id,web_name,web_catid,logo_url,web_o_url,commission,total");
$req->setWtype(1);
$req->setCatid(join(",",(1..26)));
my $resp = $c->execute($req);
if('HASH' eq ref($resp) && defined( $resp->{web_list}) ) {
        my $web_list= $resp->{web_list}->{web};
        my $i = 0;
        for (@$web_list) {
            print $i++, ":\n";
            print ' web_name:  ',$_->{web_name},"\n"; 
            print ' web_o_url:  ',$_->{web_o_url},"\n"; 
            print ' logo_url:  ',$_->{logo_url},"\n"; 
            print ' web_catid:  ',$_->{web_catid},"\n"; 
            print ' web_id:  ',$_->{web_id},"\n"; 
            #print Dumper($_);
            #exit if($i==1);
        }

}
#//echo "result:";

#print_r(mb_convert_encoding( $resp, 'gbk','utf-8'));
#
__END__
emar_websites_category
-------------+---------+------+-----+---------+----------------+
| Field       | Type    | Null | Key | Default | Extra          |
+-------------+---------+------+-----+---------+----------------+
| id          | int(11) | NO   | PRI | NULL    | auto_increment |
| web_id      | int(11) | NO   | MUL | NULL    |                |
| category_id | int(11) | NO   |     | NULL    |                |
| count       | int(11) | NO   |     | 0       |                |
+-------------+---------+------+-----+---------+----------------+

 emar_websites_croned;
 +-------------+--------------+------+-----+---------+----------------+
 | Field       | Type         | Null | Key | Default | Extra          |
 +-------------+--------------+------+-----+---------+----------------+
 | id          | int(11)      | NO   | PRI | NULL    | auto_increment |
 | web_id      | int(11)      | NO   | UNI | NULL    |                |
 | web_name    | varchar(128) | YES  |     |         |                |
 | web_catid   | int(11)      | YES  |     | NULL    |                |
 | logo_url    | varchar(128) | YES  |     |         |                |
 | web_url     | varchar(255) | YES  |     | NULL    |                |
 | information | text         | YES  |     | NULL    |                |
 | begin_date  | varchar(128) | YES  |     |         |                |
 | end_date    | varchar(128) | YES  |     |         |                |
 | commission  | text         | YES  |     | NULL    |                |
 +-------------+--------------+------+-----+---------+----------------+
# 
#
            #web_catid":"1","web_cname":"综合商城","web_type":"1

$VAR1 = {
          'web_name' => "\x{4ed9}\x{5b50}\x{5b9c}\x{5cb1}\x{5b98}\x{7f51}CPS",
          'web_o_url' => 'http://p.yiqifa.com/n?k=CyB9plPBrI6HWlzqWEUH2mquUZgL18H_UmUmfcbvk5DdY9P7fmLErI6H6ljmWQLmWl2SWngHWZLErJoH2mq9WJDFWNR76ZLE&e=APIMemberId&spm=139597428026718017.1.1.1',
          'logo_url' => 'http://image.yiqifa.com/ad_images/reguser/24/42/54/1370595980995.jpg',
          'web_catid' => '6',
          'web_id' => '1066'
        };
