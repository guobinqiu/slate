package Jili::Yiqifa::YiqifaOpen;
$VERSION = 0.0.1;

use warnings;
use strict;
use Moose;
use Time::Piece;
use URI::Escape;
use JSON;
use Data::Dumper;

use Jili::Yiqifa::YiqifaUtils;

#账号Key处请填写个人应用信息的key
has consumerKey => ( is => 'rw', isa => 'Str' ); 

#账号secret处请填写个人应用信息的secret
has consumerSecret => ( is => 'rw', isa => 'Str' ); 

#开放平台api的入口
has  gatewayUrl =>(is=>'ro', isa=>'Str', default=>'http://openapi.yiqifa.com/api2');

#输出数据的格式,xml或是json
has format => ( is => 'rw', isa => 'Str' , default=>'json');

#API版本号
has  apiVersion =>(is=>'ro', isa=>'Str', default=>'2.0');

#SDK版本号
has  sdkVersion =>(is=>'ro', isa=>'Str', default=>'eop-sdk4php');


sub curl {
    my ($self, $url,$consumerKey,$consumerSecret) = @_;
    my $result = Jili::Yiqifa::YiqifaUtils::sendRequest($url,$consumerKey,$consumerSecret);
    return $result;
}

sub execute {
    my ($self, $request)  = @_;
    my $ts = localtime->strftime("%Y-%m-%d %H:%M:%S");
    #组装系统参数
    my $sysParams = {"consumerKey" => $self->{consumerKey},
        "v" => $self->{apiVersion},
		"format" => $self->{format},
		"method" => $request->getApiMethodName(),
        "timestamp" => $ts, 
        "partner_id" => $self->{sdkVersion},
    };

    #获取业务参数
    my $apiParams = $request->getApiParams();
    my $requestUrl = sprintf('%s/%s.%s', $self->{gatewayUrl} , $sysParams->{"method"} , $sysParams->{"format"});

    $requestUrl = $requestUrl."?";

    foreach my $apiParammKey ( keys $apiParams  )
    {
        my $apiParamValue = $apiParams->{$apiParammKey} ;
        $requestUrl .= "$apiParammKey=" .uri_escape($apiParamValue). "&";
    }
    $requestUrl = substr($requestUrl, 0, -1);
    my $resp = $self->curl($requestUrl, $self->{consumerKey},$self->{consumerSecret});

    #解析TOP返回结果
    if ($self->{format} eq 'json') {
        my $json = JSON->new->utf8;
        $resp =~ s/,}/}/g;

        # print $resp;
        my $json_data = $json->decode($resp);
        if( defined $json_data->{response} ) {
            return $json_data->{response};
        }
        print $resp,"\n";
        return '' ;
    } elsif('xml' eq $self->{format}) {
        return $resp;
    }
}

1;

__END__
function __construct($key=YQF_C_KEY,$secret=YQF_C_SECRET) {        
public function curl($url,$consumerKey,$consumerSecret)
public function execute($request)


        # {"errors":{"error":[{"request":"http://openapi.yiqifa.com/api2/open.website.category.get.json","error_code":"C0006","msg":"/api2/open.website.category.get.json:request url is not validate ;"}]}}
