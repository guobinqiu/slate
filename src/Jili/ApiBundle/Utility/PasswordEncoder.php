<?php
namespace Jili\ApiBundle\Utility;
/**
 * パスワードエンコード用static関数群
 *
 * @package    opinions
 * @subpackage lib
 * @author     SUZUKI Yoshihiro <yoshihiro_suzuki@ecnavi.co.jp>
 * @version    SVN: $Id: absPasswordEncoder.class.php 11278 2011-11-07 08:36:48Z y-ohyama $
 */

class PasswordEncoder
{

    /**
     * パスワード生成
     * @param string encode type
     * @param string plain password
     * @param string salt
     * @return string encoded password
     */
    public static function encode($function_name, $plain_password, $salt = '')
    {
        $encoded_password = '';
        if (is_string($plain_password) && $plain_password !== '') {
            switch ($function_name) {
            case 'md5':
                $encoded_password = self::encode_md5($plain_password, $salt);
                break;
            case 'md5_plain':
                // 素でmd5 1回のみ。慢慢走流し込み用。
                $encoded_password = md5($plain_password);
                break;
            case 'sha1':
                $encoded_password = self::encode_sha1($plain_password, $salt);
                break;
            case 'sha1_plain':
                $encoded_password = sha1($plain_password);
                break;
            case 'blowfish':
                $resource = mcrypt_module_open(MCRYPT_BLOWFISH, '',  MCRYPT_MODE_CBC, '');
                $ivsize = mcrypt_enc_get_iv_size($resource);
                $iv = substr(md5($salt), 0, $ivsize);
                $keysize = mcrypt_enc_get_key_size($resource);
                $key = substr($salt, 0, $keysize);
                mcrypt_generic_init($resource, $key, $iv);
                $encoded_password = base64_encode(mcrypt_generic($resource, $plain_password));
                mcrypt_generic_deinit($resource);
                mcrypt_module_close($resource);
                break;
            default:
                throw new \Exception('invalid encode type');
            }
        } else {
            throw new \Exception('invalid password');
        }

        return $encoded_password;
    }

    public static function encode_md5($plain_password, $salt = '')
    {
        $encoded_password = md5($plain_password . $salt);
        for ($i = 0; $i < 30; $i++) {
            $encoded_password = md5($encoded_password . $salt);
        }
        return $encoded_password;
    }

    public static function encode_sha1($plain_password, $salt = '')
    {
        $encoded_password = sha1($plain_password. $salt);
        for ($i = 0; $i < 30; $i++) {
            $encoded_password = sha1($encoded_password. $salt);
        }
        return $encoded_password;
    }

    /**
     * salt生成
     * @return string token
     */
    public static function genSalt($salt = 'salt')
    {
        $token = md5(rand() . ':' . microtime() . $salt);

        for ($i = 0; $i < 10; $i++) {
            $token = md5($token . $salt);
        }

        return $token;
    }

    /**
     * パスワードの復号
     *
     * @param unknown_type $encoded_password
     * @param unknown_type $salt
     */
    public static function decode($function_name, $encoded_password, $salt)
    {
        $decoded_password = $encoded_password;
        if (empty($encoded_password)) {
            throw new \Exception('invalid password');
        }
        switch ($function_name) {
            case 'blowfish':
                $resource = mcrypt_module_open(MCRYPT_BLOWFISH, '',  MCRYPT_MODE_CBC, '');
                $ivsize = mcrypt_enc_get_iv_size($resource);
                $iv = substr(md5($salt), 0, $ivsize);
                $keysize = mcrypt_enc_get_key_size($resource);
                $key = substr($salt, 0, $keysize);
                mcrypt_generic_init($resource, $key, $iv);
                $base64_decrypted_password = base64_decode($encoded_password);
                $decoded_password = rtrim(mdecrypt_generic($resource, $base64_decrypted_password), "\0");
                mcrypt_generic_deinit($resource);
                mcrypt_module_close($resource);
                break;
            default:
        }

        return $decoded_password;
    }

}


