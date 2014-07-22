<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class BaseListRequest extends BaseRequest implements  ListRequestInterface
{
  protected $page_size;
  protected $total;

  public function getTotal()
  {
      return $this->total;
  }

  public function setPageSize($count)
  {
    $this->page_size = (int)  $count;
    return $this;
  }
}
