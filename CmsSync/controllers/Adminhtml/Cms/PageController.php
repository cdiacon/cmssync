<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 06/05/2013
 * Time: 15:56
 * 
 */
class CalinDiacon_CmsSync_Adminhtml_Cms_PageController extends Mage_Adminhtml_Controller_Action
{
    protected $_nodes = array();



    public function syncAction()
    {
        $params = $this->getRequest()->getParams();

        if(isset($params['page_id'])){

            // page id
            $pageId = $params['page_id'];

            $result = Mage::getModel('CalinDiacon_CmsSync_Model_Page')->sync($pageId);

            if($result['errors'])
                Mage::getSingleton('adminhtml/session')->addError($result['message']);

        }

        $this->_redirectReferer();
    }
}