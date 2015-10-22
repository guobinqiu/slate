<?php

namespace Jili\BackendBundle\Tests\Utility;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Jili\BackendBundle\Utility\VoteImageResizer;

class VoteImageResizerTest extends KernelTestCase
{

    /**
     * @group admin_vote
     */
    public function testResizeImage()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $container = static::$kernel->getContainer();
        $config = $container->getParameter('game_eggs_breaker');
        $path = $container->getParameter('cache_data_path');

        $root_dir = static::$kernel->getContainer()->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures';

        $image = $fixture_dir . '/test_vote.jpg';
        $targetDir = $fixture_dir . '/test_vote/';
        $relativePath = '5/2/52test_vote_s.jpg';
        $sidePx = 90;

        VoteImageResizer::resizeImage($image, $targetDir, $relativePath, $sidePx);

        $this->assertFileExists($targetDir . $relativePath);
        unlink($targetDir . $relativePath);
        $this->assertFileNotExists($targetDir . $relativePath);

        rmdir($targetDir . '5/2');
        rmdir($targetDir . '5');
        rmdir($targetDir);
    }
}
