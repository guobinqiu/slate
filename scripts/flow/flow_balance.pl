#!/usr/bin/perl
use strict;
use warnings;
use LWP::Simple qw(get);
use Digest::MD5 qw(md5 md5_hex md5_base64);
use JSON;
use Net::SMTP;
use Authen::SASL;
use Tie::File;
use Log::Lite qw(logrotate logmode logpath log);
########配置信息#############################################################
#设置日志路径
logpath("/tmp");

#SMTP配置信息#
my @config;
tie @config, "Tie::File", "/Users/vct_se/config/mail_config.conf";
my @mail_config = ($config[1], $config[3], $config[5], $config[7]);
untie @mail_config;

#配置api接口信息
my @api;
tie @api, "Tie::File", "/Users/vct_se/config/api.conf";
my $custom_sn = $api[1];
my $key = $api[3];
untie @api;

#设置监控余额阀值
my @balance;
tie @balance, "Tie::File", "/Users/vct_se/config/balance.conf";
my $threshold;
$threshold = $balance[1];
untie @balance;

#发送邮件的函数#
sub send_mail {
    my($mail_host, $mail_from, $mail_password, $mail_to, $subject, $mail_text) = @_;

    my $smtp = Net :: SMTP-> new ($mail_host, Timeout => 120);
    $smtp->auth($mail_from, $mail_password);
    #判断连接smtp服务器是否成功
    if ($smtp->to($mail_to)) {
        #连接smtp服务器成功
        $smtp->data();
        $smtp->datasend("To: $mail_to\n");
        $smtp->datasend("From: $mail_from\n");
        $smtp->datasend("Subject: $subject\n");
        $smtp->datasend("\n");
        $smtp->datasend("$mail_text\n");
        #判断发送邮件是否成功
        if ($smtp->dataend()) {
            #发送邮件成功,记录日志
            log("MAIL_SEND", "发送成功");
        } else {
            #发送邮件失败,记录日志
            log("MAIL_SEND_ERROR", "发送失败");
            print "send error";
            my $send_error_return = `/usr/bin/curl -X POST --data-urlencode 'payload={"channel": "#vct-alert-91jili", "username": "jili_perl", "text": "email_send_failed"}' https://hooks.slack.com/services/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`;
            if ($send_error_return != 0) {
                log("slack_error", "无法连接slack");
            }
        }
    } else {
        #连接smtp服务器失败
        log("MAIL_ERROR", $smtp->message());
        my $smtp_error_return = `/usr/bin/curl -X POST --data-urlencode 'payload={"channel": "#vct-alert-91jili", "username": "jili_perl", "text": "connect_smtp_failed"}' https://hooks.slack.com/services/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`;
        if ($smtp_error_return != 0) {
            log("slack_error", "无法连接slack");
        }
    }
}
###########################################################################

#定义api接口地址
my $url = 'http://59.83.33.14/custom_balanceV1.php';
my $enctext = md5_hex("custom_sn=$custom_sn$key");

#判断api接口是否连接成功
if (my $money_json = get("$url?custom_sn=$custom_sn&enctext=$enctext")) {
    #连接api成功,取得当前余额
    my $money_hash = decode_json($money_json);
    my $money = $money_hash-> {
        'balance' };

    #判断余额阀值
    if ($money <= $threshold) {
        #余额低于余额阀值的情况:
        my $subject = '接口余额不足,请充值';
        my $mail_text = '接口余额不足,当前余额为:' . $money;
        & send_mail(@mail_config, $subject, $mail_text);
    }
} else {
    #连接api失败
    my $subject_api = '请求api接口失败';
    my $mail_text_api = '请求api接口失败,无法查询余额';
    & send_mail(@mail_config, $subject_api, $mail_text_api);

}