<?php
namespace Wenwen\AppBundle\Utility;

class SopValidator {
    private $params;
    private $errors;

    public function __construct($params) {
        $this->params = $params;
        $this->errors  = array();
    }

    public function validate(){
        # app_mid required
        if (!isset($this->params['app_mid']) || trim($this->params['app_mid']) === '' ){
            array_push($this->errors, 'app_mid is required');
        }
        # hash required
        if (!isset($this->params['hash']) || trim($this->params['hash']) === '' ) {
            array_push($this->errors, 'hash is required');
        }
        # name required
        if (!isset($this->params['name']) || trim($this->params['name']) === '' ) {
            array_push($this->errors, 'name is required');
        }
        # time must be number
        if (!isset($this->params['time']) || !preg_match('/\A\d+\z/', $this->params['time'])) {
            array_push($this->errors, 'time is invalid');
        }
        # sig required
        if (!isset($this->params['sig']) || !preg_match('/\A[0-9a-f]+\z/', $this->params['sig'])) {
            array_push($this->errors, 'sig is invalid');
        }

        return count($this->errors) === 0;
    }

    public function getErrors() {
        return $this->errors;
    }
}
