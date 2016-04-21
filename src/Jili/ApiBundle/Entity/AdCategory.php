<?php

namespace Jili\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdCategory
 *
 * @ORM\Table(name="ad_category")
 * @ORM\Entity(repositoryClass="Jili\ApiBundle\Repository\AdCategoryRepository")
 */
class AdCategory
{
    const ID_91WENWEN_POINTS = 8;//91问问积分
    const ID_AMAZON = 10;//亚马逊礼品卡
    const ID_ALIPAY = 11;//支付宝
    const ID_MOBILE = 12;//手机费
    const ID_FLOW = 24;//流量包
    const ID_QUESTIONNAIRE_COST = 92;//问卷回答
    const ID_QUESTIONNAIRE_EXPENSE = 93;//快速问答


    /**
     * @var const
     *  寻宝箱的id.
     */
    const ID_GAME_SEEKER = 30;

    /**
     * @var const
     *  砸金蛋id.
     */
    const ID_GAME_EGGS_BREAKER = 31;

    /**
     * @var const
     *  多麦id.
     */
    const ID_DUOMAI = 23;

    /**
     * @var const
     *  成果CPA.
     */
    const ID_ADW_CPA = 1;

    /**
     * @var const
     *  成果CPS.
     */
    const ID_ADW_CPS = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category_name", type="string", length=45, nullable=true)
     */
    private $categoryName;

    /**
     * @var string
     *
     * @ORM\Column(name="asp", type="string", length=64, nullable=true)
     */
    private $asp;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=100, nullable=true)
     */
    private $displayName;



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
     * Set categoryName
     *
     * @param string $categoryName
     * @return AdCategory
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * Set asp
     *
     * @param string $asp
     * @return AdCategory
     */
    public function setAsp($asp)
    {
        $this->asp = $asp;

        return $this;
    }

    /**
     * Get asp
     *
     * @return string
     */
    public function getAsp()
    {
        return $this->asp;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return AdCategory
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getIsEmarCps()
    {
        return $this->getAsp() === 'emar' && strtolower($this->getCategoryName() )=== 'cps';
    }
}
