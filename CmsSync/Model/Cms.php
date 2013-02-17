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
    protected $blockId = false;
    protected $isMaster = false;
    protected $isEnabled = false;
    /**
     * Sync the static block
     * @param $blockId
     */
    public function syncStaticBlock($blockId)
    {

        /**
         * verify the source and get nodes info
         **/
        $validNodes = $this->getEnabledNodes();



        if($blockId){

            $model = Mage::getModel('cms/block');

            if(! $id = $model->load($blockId)){
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This block no longer exists!'));
                $this->_redirect('*/*');
                return;
            }

            $identifier = $model->getIdentifier();
            $title = $model->getTitle();
            $content = $model->getContent();

            $node1 = Mage::getStoreConfig('cmssync/general/urlone');
            $username = Mage::getStoreConfig('cmssync/general/usernameone');
            $passwordone = Mage::getStoreConfig('cmssync/general/passwordone');



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

            die;


        }

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

                if ($node->isValid){
                    $nodeMapper->nodes[] = $node;
                }
                if (!$onemore)
                    break;
            }
Mage::log('number of nodes : ' . $i);

        }else{

            Mage::throwException('The Source must be master and enabled!');
        }
        return $nodeMapper;
    }


}