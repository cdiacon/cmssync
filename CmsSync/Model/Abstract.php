<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 05/05/2013
 * Time: 19:23
 * 
 */
class CalinDiacon_CmsSync_Model_Abstract extends Mage_Core_Model_Abstract
{
    protected $isEnabled = false;
    protected $isMaster = false;
    // block id, page id
    protected $id;
    protected $pageId;
    protected $storeIds = array();
    public $title ;
    public $content;
    protected $proxy;
    protected $session;
    public $node;
    public $identifier;

    protected $_creation_time;
    protected $_update_time;
    public    $is_active;
    protected $_storeId;
    protected $_stores = array();
    public $storeCodes = array();



    /**
     * check if remote has most updated info
     * @param $remoteUpdateTime
     * @return bool
     */
    public function isLater($remoteUpdateTime)
    {
        $remoteTime = new Zend_Date($remoteUpdateTime);
        $localTime = new Zend_Date($this->updateTime);
        if($localTime->isLater($remoteTime)){
            return true;
        };
        return false;
    }

    /**
     * Get nodes data
     * @return array|bool
     */
    public function getValidNodes()
    {

        $nodes = unserialize(Mage::getStoreConfig('cmssync/general/nodes'));

        if(! empty($nodes)){

            $data = array();
            foreach ($nodes as $node){

                $nodeModel = new CalinDiacon_CmsSync_Model_Node();

                $nodeModel->setUrl($node['apiurl']);
                $nodeModel->setUsername($node['username']);
                $nodeModel->setPassword($node['password']);

                // save valid
                if($nodeModel->isValid())
                    $data[] = $nodeModel;
            }
            return $data;
        }

        return false;
    }


}