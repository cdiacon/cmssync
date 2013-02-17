<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 04/02/2013
 * Time: 23:49
 *
 */
class CalinDiacon_CmsSync_Adminhtml_Cms_BlockController extends Mage_Adminhtml_Controller_Action
{
    protected $_nodes = array();

    public function syncAction()
    {
        $params = $this->getRequest()->getParams();

        $this->syncByBlockId($params['block_id']);

    }

    protected function syncByBlockId($blockId = false)
    {
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


}