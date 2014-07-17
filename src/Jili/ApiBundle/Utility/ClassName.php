<?php
namespace Jili\ApiBundle\Utility;

class ClassName {
    private $name;
    private $userId;

    public function __construct($name, $userId) {
        $this->name = $name;
        $this->userId = $userId;
    }

    public function getClassName() {
        $suffix = substr($this->userId, -1, 1);
        return sprintf($this->name . '%02d', $suffix);
    }
}
?>
