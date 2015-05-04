package Jili::Yiqifa::YiqifaUtils;
$VERSION = v0.0.1;

use warnings;
use strict;

use Moose;
use URI::Escape;
use WWW::Curl::Easy;
use Digest::SHA qw(sha1_hex);
use MIME::Base64;
use Encode qw/from_to/;

has BASE_URL => ( is => 'rw', isa => 'Str', default=>'http://openapi.yiqifa.com/api2' );

sub sendRequest {
    my ( $url,$key,$secret) = @_; 

    my $au = generateOauth( ($url,$key,$secret));

    my $curl = WWW::Curl::Easy->new;
    $curl->setopt(CURLOPT_URL, $url);
    $curl->setopt(CURLOPT_HTTPHEADER, ["Authorization: ".$au]);
    $curl->setopt(CURLOPT_USERAGENT, "http://open.yiqifa.com");
    $curl->setopt(CURLOPT_CONNECTTIMEOUT, 10);
    $curl->setopt(CURLOPT_TIMEOUT, 30);

    my $response_body;
    $curl->setopt(CURLOPT_WRITEDATA,\$response_body);

    # Starts the actual request
    my $retcode = $curl->perform;

    # Looking at the results...
    if ($retcode == 0) {
        #$response_body = Encode::decode('GBK',$response_body);
        from_to($response_body, 'GBK', 'UTF-8');
        my $response_code = $curl->getinfo(CURLINFO_HTTP_CODE);
        # judge result and next action based on $response_code
        return $response_body; #Encode::encode('UTF-8', $response_body); 
    } else {
        # Error code, type of error, error message
        print("An error happened: $retcode ".$curl->strerror($retcode)." ".$curl->errbuf."\n");
    }
}   

sub hmacsha1 {
    my ($key,$data) = @_;
    my $blocksize=64;
    my $hashfunc= 'sha1_hex';
    my $keylen =length($key); 
    if ( $keylen > $blocksize) {
        $key=pack('H*',sha1_hex($key));
    } 
    $keylen =length($key); 
    if ($keylen < $blocksize) {
        my $suffix = chr(0x00) x ( $blocksize - ( $keylen % $blocksize ) ) ;
        $key = $key.$suffix ;
    }

    my $ipad=chr(0x36) x $blocksize;
    my $opad=chr(0x5c) x $blocksize;
    my $hmac = pack(
        'H*',sha1_hex(
            ($key^$opad).pack(
                'H*',sha1_hex(
                    ($key^$ipad).$data
                )
            )
        )
    );

    return encode_base64($hmac);    
}


sub generateOauth {
    my ($url,$key,$secret)= @_;
    my $authparam = generateAuthParams($key,$secret);       
    my $urlParsed = parseGetParams($url);
    my $params = {%$authparam, %$urlParsed};
    my $basestr = generateBaseStr($url,$params);
    my $tk = $secret."&openyiqifa";
    my $sign = hmacsha1($tk,$basestr);     
    my $str = "";
    foreach my $k (keys %$authparam ){
        my $v = $authparam->{$k};
        if(length($str) == 0  ) { 
            $str .=  $k."=\"".uri_escape($v)."\"";
        } else {
            $str.= (",".$k."=\"".uri_escape($v)."\"");    
        }

    }

    $str = "OAuth ".$str.",oauth_signature=\"".uri_escape($sign)."\"";
    return $str;
}

sub generateAuthParams{
    my($key,$secret) = @_;
    my $ts = time();
    my $nonce = $ts + int(rand() * 100);
    my $authparam = {
        "oauth_consumer_key"=>$key,
        "oauth_signature_method"=>"HMAC-SHA1",
        "oauth_timestamp"=>$ts,
        "oauth_nonce"=>$nonce,
        "oauth_version"=>"1.0",
        "oauth_token"=>"openyiqifa"
    };       
    return $authparam;    
}

#sub  generateRequestStr($url){
sub  generateBaseStr {
    my ($url,$params) = @_;
    my @keys = sort(keys (%$params ));
    my @params_sorted  ;
    foreach(@keys) {
        push @params_sorted, ( $_.'='. uri_escape($params->{$_}));
    }
    my $basestr= 'GET&'.uri_escape(constructRequestURL($url) ).'&'. join('&', @params_sorted);;
    return $basestr;
}

#sub  normalizeRequestParameters($params){

sub sortParams {
    my ($params) = @_;
    my $newparams = [];
    my @keys = sort(keys (%$params ));
    foreach(@keys ){
        $newparams->[$_] = $params->{$_};    
    } 

    return $newparams;  
}

sub constructRequestURL{
    my($url) = @_;
    my $i = index($url , '?');
    if(!$i) {
        return $url;

    } else {
        return substr($url, 0 , $i);
    }
}

sub parseGetParams {
    my ($url) = @_;

    my $params = {}; 
    my $i = index($url,"?");

    if(!$i){
        return $params;
    }

    my  $sp = split("&",substr($url,$i+1, length($url)));

    foreach my $p($sp ){
        my @spi = split("=",$p);
        if( $#spi + 1 > 1 ){
            $params->{uri_unescape( $spi[0])} = uri_unescape( $spi[1]);
        }
    }
    return $params;
}

1;
__END__
    static function getBaseUrl(){
    static function sendRequest($url,$key,$secret){
    static function hmacsha1($key,$data) {
    static function generateOauth($url,$key,$secret){
    static function generateAuthParams($key,$secret){
    static function generateRequestStr($url){
    static function generateBaseStr($url,$params){
    static function normalizeRequestParameters($params){
    static function sortParams($params){
    static function constructRequestURL($url){
    static function parseGetParams($url){

