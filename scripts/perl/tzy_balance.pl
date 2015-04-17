#!/usr/bin/perl
use strict;
use warnings;
use LWP::Simple qw(get);
use Digest::MD5 qw(md5 md5_hex md5_base64);
use JSON;
use Net::SMTP;
use Authen::SASL;
use YAML::XS;
use Lib::SMTP;
use Log::Lite qw(logrotate logmode logpath log);


#定义log文件目录
logpath("./logs");

my $config_return=eval{YAML::XS::LoadFile('./config/tzy_balance.yaml')};
if (defined($config_return)) {
    my $mail_to=$config_return->{mail_send}->{to};
    my $balance=$config_return->{interface}->{balance};
    #加载api相关得配置信息
    my $api_url=$config_return->{interface}->{url};
    my $custom_sn=$config_return->{interface}->{custom_sn};
    my $prv_key=$config_return->{interface}->{prv_key};
    #拼接请求api接口的字符串
    my $enctext=md5_hex("custom_sn=$custom_sn$prv_key");
    #判断api接口是否连接成功
    if(my $money_json=get("$api_url?custom_sn=$custom_sn&enctext=$enctext")){
        #api连接成功
        my $money_hash=decode_json($money_json);
        my $money=$money_hash->{balance};
        log("tzy_api","天纵云流量包接口连接正常");
        #判断账户余额是否低于阈值
        if($money < $balance){
            #余额低于阈值
            my $mail_subject="天纵云流量包账户余额不足";
            my $mail_content="天纵云流量包账户余额不足,当前余额为".$money;
            my $send_return=sendmail($mail_to,$mail_subject,$mail_content);
            if ($send_return == 1) {
                ###tzy_api###
                log("tzy_api","[天纵云流量包余额监控]邮件发送成功");
                log("tzy_api","天纵云流量包余额不足,余额为".$money);
            }else{
                log("tzy_api_error","[天纵云流量包余额监控]邮件发送失败");
            }
        }else{
            #余额高于阈值
            log("tzy_api","天纵云流量包余额尚足,为".$money);
        }
    }else{
        #连接api接口失败
        my $mail_subject="无法连接天纵云流量包api接口";
        my $mail_content="无法连接天纵云流量包api接口,请联系管理员";
        log("tzy_api_error","无法连接天纵云api接口");
        my $mail_return=sendmail($mail_to,$mail_subject,$mail_content);
            if ($mail_return == 1) {
                log("tzy_api","[无法连接天纵云API接口]邮件发送成功");
            }else{
                log("tzy_api_error","[无法连接天纵云API接口]邮件发送失败");
            }

    }
}else{
    #加载配置文件失败
    log("tzy_api_error","加载YAML配置文件失败,请检查");
}
