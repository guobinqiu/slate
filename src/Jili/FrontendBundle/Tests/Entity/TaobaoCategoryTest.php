<?php
namespace Jili\FrontendBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Jili\FrontendBundle\Entity\TaobaoCategory;

class TaobaoCategoryTest extends KernelTestCase
{
    
    /**
     * @group issue_549
     */
    public function testConst()
    {
        $this->assertEquals(1 ,TaobaoCategory::SELF_PROMOTION );
        $this->assertEquals(2 ,TaobaoCategory::COMPONENTS );
    }
}

