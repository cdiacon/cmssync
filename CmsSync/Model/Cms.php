<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 17/02/2013
 * Time: 14:08
 * 
 */
class CalinDiacon_CmsSync_Model_Cms
{
    /**
     * source if is master
     * @var bool
     */
    protected $isMaster = false;
    /**
     * source if is enabled
     * @var bool
     */
    protected $isEnabled = false;
    /**
     * Sync the static block
     * @param $blockId
     */
    public $blockId ;
    /**
     * soap client
     * @var object
     */
    public $storeIds = array();
    public $title ;
    public $content;
    protected $proxy;
    /**
     * session key
     * @var string
     */
    protected $sessionId;
    /**
     * node configuration info
     * @var object
     */
    public $node;
    public $identifier;
    public function syncStaticBlock($blockId)
    {
        /**
         * verify the source and get nodes info
         **/
        $validNodes = $this->getEnabledNodes();

        if($blockId && count($validNodes)){

            $this->blockId = $blockId;
            $modelBlock = Mage::getModel('cms/block');

            if(! $id = $modelBlock->load($blockId)){
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This block no longer exists!'));
                $this->_redirect('*/*');
                return;
            }

            $this->identifier = $modelBlock->getIdentifier();
            $this->storeIds = $modelBlock->getStoreId();
            $this->title = $modelBlock->getTitle();
            $this->content = $modelBlock->getContent();
            $this->updateTime = $modelBlock->getUpdateTime();

            foreach ($validNodes as $node) {

                $this->node = $node;
                $isNew = $this->checkForNewBlock();

                $isEnabled = $this->proxy->call($this->sessionId, 'cms_api.is_enabled');// 0 - disabled
                $source = $this->proxy->call($this->sessionId, 'cms_api.get_source');// 0 - node ; 1 - master

                if ($isEnabled && ! $source){
                    if ($isNew){

                        /**
                         * Create remote blocks
                         */
                        $createdResult = $this->proxy->call($this->sessionId, 'cms_api.block_create', array($modelBlock->getData()));

                    }else{

                        //@todo make the block an object and then compare
                        $remoteBlock = $this->proxy->call($this->sessionId, 'cms_api.block_info', $this->identifier);// array of info for the remote block

                        $isLater = $this->isLater($remoteBlock['update_time']);

                        if($isLater || $this->node->override){

                            $this->proxy->call($this->sessionId, 'cms_api.block_update', array($modelBlock->getData()));

                        }else{

                            Mage::log('skipping the remote because is newer :'  .$this->node->override);
                        }

                    }
                }else{
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Remote node is disabled or is using different store!'));
                    Mage::throwException('The remote node is not enabled stop furthe actions');
                }

            }

        }else{
            Mage::throwException('No valid nodes or invalid block.');
        }

    }

    /**
     * check the remote node block
     * and cancel if the node is disabled
     * @return bool
     */
    public function checkForNewBlock()
    {
        $this->prepareConnection();


        $new = $this->proxy->call($this->sessionId, 'cms_api.block_is_new', $this->identifier);
        if ($new){

            return true;
        }else{

            return false;
        }
    }

    /**
     * Connect to the current node
     * @return bool
     */
    protected function prepareConnection()
    {
        $this->proxy = new Zend_Soap_Client($this->node->getUrl());
        $this->sessionId = $this->proxy->login($this->node->getUsername(), $this->node->getPassword());
        if ($this->sessionId)
            return true;
        else
            return false;
    }

    /**
     *
     * Get defined nodes to sync
     * @return CalinDiacon_CmsSync_Model_NodeMapper
     */
    public function getEnabledNodes()
    {
        $this->isEnabled = (Mage::getStoreConfig('cmssync/general/enabled'))? Mage::getStoreConfig('cmssync/general/enabled')  : false;
        $this->isMaster = (Mage::getStoreConfig('cmssync/general/source'))? Mage::getStoreConfig('cmssync/general/source') : false;
        $nodeMapper = new CalinDiacon_CmsSync_Model_NodeMapper();

        if ($this->isEnabled && $this->isMaster){

            //@todo parse xml and get number of nodes
            for($i = 1; $i <= 3;$i++){

                $node = new CalinDiacon_CmsSync_Model_Node();
                $url = Mage::getStoreConfig('cmssync/general/url_' . $i);
                $username = Mage::getStoreConfig('cmssync/general/username_' . $i);
                $password = Mage::getStoreConfig('cmssync/general/password_' . $i);
                $onemore = Mage::getStoreConfig('cmssync/general/onemore_'. $i);
                $override = Mage::getStoreConfig('cmssync/general/override_'. $i);

                $node->setUrl($url);
                $node->setUsername($username);
                $node->setPassword($password);
                $node->setOverride($override);

                if ($node->isValid()){
                    $nodeMapper->nodes[] = $node;
                }
                if (!$onemore)
                    break;
            }
        }else{

            Mage::throwException('The Source must be master and enabled!');
        }
        return $nodeMapper->nodes;
    }

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


}