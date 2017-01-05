<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SurveyGMOControllerTest extends WebTestCase
{
    public function test() {
        $panelistId = '2067715';
        $panelCode = '94';
        $randomString = strtotime('now');
        $encryptedID = "{$panelistId}:{$panelCode}:{$randomString}";
        $encryptKey = 'gB9d280c6fd0C398';
        $crypt = $this->encrypt_blowfish($encryptedID, $encryptKey);
        echo PHP_EOL . $crypt;
    }

    private function decrypt_blowfish($data, $key) {
        $data = pack("H*", $data);
        $res = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data , MCRYPT_MODE_ECB);
        return $res;
    }

    private function encrypt_blowfish($data, $key) {
        $blockSize = mcrypt_get_block_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $padding = $blockSize - (strlen($data) % $blockSize);
        $data .= str_repeat(chr($padding), $padding);
        $cipherText = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $data, MCRYPT_MODE_ECB);
        return bin2hex($cipherText);
    }
}