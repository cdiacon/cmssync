<?php

class CalinDiacon_CmsSync_Model_Node extends Mage_Core_Model_Abstract
{
    private  $_url;
    protected  $_username;
    protected  $_password;
    protected  $_isValid = true;
    public     $lastone = false;
    public     $override = false;

    /**
     * validate url
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $result = Zend_Uri::check($url);
        if ($result){
            $this->_url = $url;
        }else{
            Mage::getModel('adminhtml/session')->addError('Node url is invalid : ' . $url);
            $this->_isValid = false;
        }
        return $this;
    }

    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    public function isValid()
    {
        return $this->_isValid;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function getPassword()
    {
        return $this->_password;
    }


}