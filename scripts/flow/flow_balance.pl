#!/usr/bin/perl
#
# @File newPerlFileTemplate.pl
# @Author vct_se
# @Created 2015-3-3 16:16:45
#

use strict;
use warnings;
use LWP::Simple qw(get);
use Digest::MD5 qw(md5 md5_hex md5_base64);
use JSON;
use Net::SMTP;
use Authen::SASL;

#定义接口地址
my $url='http://59.83.33.14/custom_balanceV1.php';
#定义我们的custom号码
my $custom_sn='B772AA94ABDC6BEE';
#定义我们的私有key
my $key='5E703068E764D3DC';
#生成加密数据,请求api接口
my $enctext=md5_hex("custom_sn=$custom_sn$key");
#获得api接口的返回数据
my $money_json=get("$url?custom_sn=$custom_sn&enctext=$enctext");
#将得到的json数据转成哈希数据
my $money_hash=decode_json($money_json);
#获取余额
my $money=$money_hash->{'balance'};
if($money <= 10000){
#定义smtp信息
    my $mail_host='smtp.exmail.qq.com';
    my $mail_from='xujf@voyagegroup.com.cn';
    my $mail_password='8416032abc';
    my $mail_to='xujf@voyagegroup.com.cn';
    my $subject='接口余额不足,请充值';
    my $mail_text='接口余额不足,当前余额为:'.$money;
    my $smtp=Net::SMTP->new($mail_host,Timeout => 120);
    $smtp->auth($mail_from,$mail_password);
#发送邮件
    $smtp->to($mail_to);
    $smtp->data();
    $smtp->datasend("To: $mail_to\n");
    $smtp->datasend("From: $mail_from\n");
    $smtp->datasend("Subject: $subject\n");
    $smtp->datasend("\n");
    $smtp->datasend("$mail_text\n");
    $smtp->dataend();
}