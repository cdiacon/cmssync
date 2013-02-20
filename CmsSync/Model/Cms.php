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

            foreach ($validNodes as $node) {

                $this->node = $node;
                $noRemoteFound = $this->remoteExists();
                if ($noRemoteFound){
Mage::log('no remote block found : ' . $node->getUrl());
                }else{
Mage::log('this block: ' . $this->identifier . ',  already exists: ' . $node->getUrl());
                    //@todo make the block an object and then compare

                    $remoteBlock = $this->proxy->call($this->sessionId, 'cms_api.block_info', $this->blockId);// array of info for the remote block

Mage::log($remoteBlock);
                    die;
                }

            }


            $node1 = "http://calin.wineglassworld.dev/index.php/api/soap/?wsdl=1";

            $options["connection_timeout"] = 255;
            $options["location"] = $node1;
            $options['trace'] = 1;



            $proxy = new Zend_Soap_Client($node1);
            $sessionId = $proxy->login('admin', 'admin123');
            $email = 'cdiacon@gmail.com';
            $store = 0;

            //$result = $proxy->catalogCategoryTree($sessionId);



            //$proxy = new SoapClient($node1);
            //$sessionId = $proxy->login($username, $passwordone);

            //$data = $proxy->call($session, 'catalog.product_info', array(1));
            //$data  = $proxy->catalogCategoryTree($sessionId);
            $data = $proxy->call($sessionId , 'cms_api.info', 'THIS TEXT IS AWSOME!!!');//, array(1));// array($email, $store));
//$data=  $proxy->call($sessionId, 'customer.list', array(array()));


            //$data = $proxy->call($sessionId, 'config.api/get', array());




            var_dump($data);



        }else{
            Mage::throwException('No valid nodes or invalid block');
        }

    }

    /**
     * check the remote node block
     * and cancel if the node is disabled
     * @return bool
     */
    public function remoteExists()
    {
        $this->prepareConnection();

        $isEnabled = $this->proxy->call($this->sessionId, 'cms_api.is_enabled');// 0 - disabled
        $source = $this->proxy->call($this->sessionId, 'cms_api.get_source');// 0 - node ; 1 - master

        if ($isEnabled && ! $source){

            $data = array(
                'identifier' => $this->identifier,
                'storeIds' => $this->storeIds
            );

            $remoteExists = $this->proxy->call($this->sessionId, 'cms_api.block_is_new', $this->identifier, array($this->storeIds));
Mage::log('looking for the identifier : ' . $this->identifier);
Mage::log('the actual responce :' . $remoteExists);
            if ($remoteExists){

                return false;
            }else{
                return true;
            }

        }else{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Remote node is disabled!'));
            Mage::throwException('The remote node is not enabled stop furthe actions');
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

                $node->setUrl($url);
                $node->setUsername($username);
                $node->setPassword($password);

                if ($node->isValid()){
                    $nodeMapper->nodes[] = $node;
                }
                if (!$onemore)
                    break;
            }
Mage::log('number of valid nodes : ' . count($nodeMapper));
        }else{

            Mage::throwException('The Source must be master and enabled!');
        }
        return $nodeMapper->nodes;
    }


}