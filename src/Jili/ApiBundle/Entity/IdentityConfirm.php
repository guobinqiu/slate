<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IdentityConfirm
 *
 * @ORM\Table(name="identity_confirm")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\IdentityConfirmRepository")
 */
class IdentityConfirm
{
    public function __construct()
    {
        $this->identityValidateTime = new \DateTime();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

     /**
     * @var string
     *
     * @ORM\Column(name="identity_card", type="string" ,length=50)
     */
    private $identityCard;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="identity_validate_time", type="datetime")
     */
    private $identityValidateTime;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return IdentityConfirm
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }


    /**
     * Set identityCard
     *
     * @param string $identityCard
     * @return IdentityConfirm
     */
    public function setIdentityCard($identityCard)
    {
        $this->identityCard = $identityCard;

        return $this;
    }

    /**
     * Get identityCard
     *
     * @return string
     */
    public function getIdentityCard()
    {
        return $this->identityCard;
    }


    /**
     * Set identityValidateTime
     *
     * @param \DateTime $identityValidateTime
     * @return IdentityConfirm
     */
    public function setIdentityValidateTime($identityValidateTime)
    {
        $this->identityValidateTime = $identityValidateTime;

        return $this;
    }

    /**
     * Get identityValidateTime
     *
     * @return \DateTime
     */
    public function getIdentityValidateTime()
    {
        return $this->identityValidateTime;
    }

    public static $areas = array(
        11 => "北京", 12 => "天津", 13 => "河北", 14 => "山西", 15 => "内蒙古",
        21 => "辽宁", 22 => "吉林", 23 => "黑龙江",
        31 => "上海", 32 => "江苏", 33 => "浙江", 34 => "安徽", 35 => "福建", 36 => "江西", 37 => "山东",
        41 => "河南", 42 => "湖北", 43 => "湖南", 44 => "广东", 45 => "广西", 46 => "海南",
        50 => "重庆", 51 => "四川", 52 => "贵州", 53 => "云南", 54 => "西藏",
        61 => "陕西", 62 => "甘肃", 63 => "青海", 64 => "宁夏", 65 => "新疆",
        71 => "台湾",
        81 => "香港", 82 => "澳门",
        91 => "国外",
    );
    public static $check_digits = '10X98765432';

    public function isValid($identityCard)
    {
        if(!self::lengthIsValid($identityCard)) {
            return false;
        }

        if(!self::regionIsValid($identityCard)) {
            return false;
        }

        if(!self::birthdayIsValid($identityCard)) {
            return false;
        }

        if(!self::checkDigitIsValid($identityCard)) {
            return false;
        }

        return true;
    }

    public static function lengthIsValid($identityCard)
    {
        if (strlen($identityCard) === 15) {
            return true;
        }

        if(strlen($identityCard) === 18) {
            return true;
        }

        return false;
    }

    public static function regionIsValid($identityCard)
    {
        $region_id = self::getRegion($identityCard);

        return array_key_exists($region_id, self::$areas);
    }

    public static function birthdayIsValid($identityCard)
    {
        $birthday = self::getBirthDay($identityCard);
        return checkdate($birthday['month'], $birthday['day'], $birthday['year']);
    }

    public static function getRegion($identityCard)
    {
        return (int) mb_substr($identityCard, 0, 2);
    }

    public static function checkDigitIsValid($identityCard)
    {
        # check digit doesn't exists
        if(strlen($identityCard) !== 18) {
            return true;
        }
        $calclated = self::calcCheckDigit($identityCard);
        $check_digit = $identityCard[17];

        return ($calclated == $check_digit);
    }

    /**
     * only for length = 18
     */
    public static function calcCheckDigit($identityCard)
    {
        if(strlen($identityCard) !== 18) {
            return null;
        }

        $digits = array();
        for($i = 0; $i < mb_strlen($identityCard); $i++) {
            $digits[] = (int) $identityCard[$i];
        }
        $calc = ($digits[0] + $digits[10]) * 7
                + ($digits[1] + $digits[11]) * 9
                + ($digits[2] + $digits[12]) * 10
                + ($digits[3] + $digits[13]) * 5
                + ($digits[4] + $digits[14]) * 8
                + ($digits[5] + $digits[15]) * 4
                + ($digits[6] + $digits[16]) * 2
                + $digits[7] * 1
                + $digits[8] * 6
                + $digits[9] * 3
            ;
        $calc = $calc % 11;
        return self::$check_digits[$calc];

    }

    /**
     * 取得生日（由身份证号）
     * @param int $id 身份证号
     * @return string
     */
    private static function getBirthDay($identityCard)
    {
        switch (strlen ( $identityCard )) {
        case 15 :
            $year = "19" . substr ( $identityCard , 6, 2 );
            $month = substr ( $identityCard , 8, 2 );
            $day = substr ( $identityCard , 10, 2 );
            break;
        case 18 :
            $year = substr ( $identityCard , 6, 4 );
            $month = substr ( $identityCard , 10, 2 );
            $day = substr ( $identityCard , 12, 2 );
            break;
        }
        $birthday = array ('year' => $year, 'month' => $month, 'day' => $day );
        return $birthday;
    }



}
