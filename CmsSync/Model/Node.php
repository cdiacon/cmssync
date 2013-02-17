<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 17/02/2013
 * Time: 14:34
 * 
 */
class CalinDiacon_CmsSync_Model_Node
{
    protected  $_url;
    protected  $_username;
    protected  $_password;
    public   $lastone = false;
    public $isValid = true;

    public function setUrl($url)
    {
Mage::log('setting url ...' . $url);
        $result = Zend_Uri::check($url);
Mage::log('url validation result : ' . $result);
        if ($result){
            $this->_url = $url;
        }else{
            $this->isValid = false;
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