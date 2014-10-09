<?php
namespace Jili\BackendBundle\Tests\Services\Advertiserment;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest;

class ChanetHttpRequestTest extends KernelTestCase
{

    /**
     * @group issue_469 
     */
    public  function testIsExpired() 
    {
        $map = array(
            array('http://count.chanet.com.cn/click.cgi?a=480534&d=9340&u=33&e=105',
            <<<EOD
<script>window.location.href='http://union.dangdang.com/transfer/transfer.aspx?from=430-88571126696&backurl=http://book.dangdang.com';</script>
EOD
        ),
            array('http://count.chanet.com.cn/click.cgi?a=480534&d=92051&u=92&e=105',
<<<EOD
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<html>
<head>
<title>CHANet</title>
</head>
<body>
该广告已经结束。<br>
请点击查看如下的新广告。<br>
<div id="ad_content"></div>
<script language="Javascript">

var url=window.location.href; 
var es=/\?a=/; 
es.exec(url); 
var as_id=RegExp.rightContext; 

var content='<a href="http://count.chanet.com.cn/click.cgi?a=218&d=365160&u=default_ad" target="_blank"><IMG SRC="http://file.chanet.com.cn/image.cgi?a=218&d=365160&u=default_ad" border="0"></a>';
var new_content = '<table><tr valign="top"><td>';
new_content += content.replace(/\?a=(.+?)&/,"?a="+as_id+'&');
new_content += '</td><tr></table>'
document.getElementById('ad_content').innerHTML = new_content;
</script>
</body>
</html>
EOD
        ),
        );

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder('\Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest')
            ->setConstructorArgs(array($map[0][0]))
            ->setMethods(array('getRawReturn'))
            ->getMock();

        // Configure the stub.
        $stub->method('getRawReturn')
            ->will($this->returnValue($map[0][1]));

        $this->assertEquals(false, $stub->isExpired(), 'is not expired');

        $stub = $this->getMockBuilder('\Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest')
            ->setConstructorArgs(array($map[1][0]))
            ->setMethods(array('getRawReturn'))
            ->getMock();

        // Configure the stub.
        $stub->method('getRawReturn')
            ->will($this->returnValue($map[1][1]));

        $this->assertEquals(true, $stub->isExpired(), 'no expired');
    }
    /**
     * @group issue_469 
     */
    public  function testGetDestinationUrl() 
    {
        $map = array(
            array('http://count.chanet.com.cn/click.cgi?a=480534&d=9340&u=33&e=105',
            <<<EOD
<script>window.location.href='http://union.dangdang.com/transfer/transfer.aspx?from=430-88571126696&backurl=http://book.dangdang.com';</script>
EOD
        ),
            array('http://count.chanet.com.cn/click.cgi?a=480534&d=92051&u=92&e=105',
            <<<EOD
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<html>
<head>
<title>CHANet</title>
</head>
<body>
该广告已经结束。<br>
请点击查看如下的新广告。<br>
<div id="ad_content"></div>
<script language="Javascript">

var url=window.location.href; 
var es=/\?a=/; 
es.exec(url); 
var as_id=RegExp.rightContext; 

var content='<a href="http://count.chanet.com.cn/click.cgi?a=218&d=365160&u=default_ad" target="_blank"><IMG SRC="http://file.chanet.com.cn/image.cgi?a=218&d=365160&u=default_ad" border="0"></a>';
var new_content = '<table><tr valign="top"><td>';
new_content += content.replace(/\?a=(.+?)&/,"?a="+as_id+'&');
new_content += '</td><tr></table>'
document.getElementById('ad_content').innerHTML = new_content;
</script>
</body>
</html>
EOD
        ),
        );

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder('\Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest')
            ->setConstructorArgs(array($map[0][0]))
            ->setMethods(array('getRawReturn'))
            ->getMock();

        // Configure the stub.
        $stub->method('getRawReturn')
            ->will($this->returnValue($map[0][1]));

        $this->assertEquals('http://union.dangdang.com/transfer/transfer.aspx?from=430-88571126696&backurl=http://book.dangdang.com', $stub->getDestinationUrl(), 'parse the window.href from response');


        $stub = $this->getMockBuilder('\Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest')
            ->setConstructorArgs(array($map[1][0]))
            ->setMethods(array('getRawReturn'))
            ->getMock();

        // Configure the stub.
        $stub->method('getRawReturn')
            ->will($this->returnValue($map[1][1]));

        $this->assertEquals($map[1][0], $stub->getDestinationUrl(), 'parse the window.href from response');

    }
}

