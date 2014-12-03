<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jili\Symfony\Bundle\FrameworkBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as AbstractWebTestCase;
use Symfony\Component\Finder\Finder;

/**
 * WebTestCase is the base class for functional tests.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class WebTestCase extends AbstractWebTestCase
{
    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     *
     * @throws \RuntimeException
     */
    protected static function getKernelClass( )
    {
        $dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : static::getPhpUnitXmlDir();

        $finder = new Finder();

        $environment = isset($options['environment']) ? $options['environment'] : 'test';
        $environment = ucfirst($environment);

        $finder->name('*'.$environment.'Kernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            $finder->name('*Kernel.php')->depth(0)->in($dir);
            $results = iterator_to_array($finder);
            if (!count($results)) {
                throw new \RuntimeException('Either set KERNEL_DIR in your phpunit.xml according to http://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the WebTestCase::createKernel() method.');
            }
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;
        return $class;
    }
}
