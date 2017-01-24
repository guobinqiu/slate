<?php

namespace Wenwen\AppBundle\Utility;

class IPChecker
{
    public static function checkIp($ip, $allowIps)
    {
        if (in_array($ip, $allowIps)) {
            return true;
        }

        foreach ($allowIps as $allowIp) {
            $allowIpArr = explode('.', $allowIp);
            $ipArr = explode('.', $ip);

            if (self::matched($ipArr, $allowIpArr)) {
                return true;
            }
        }
        return false;
    }

    private static function matched(array $ipArr, array $allowIpArr)
    {
        for ($i = 0; $i < 4; $i++) {
            if ($allowIpArr[$i] != '*') {
                if ($allowIpArr[$i] != $ipArr[$i]) {
                    return false;
                }
            }
        }
        return true;
    }
}
