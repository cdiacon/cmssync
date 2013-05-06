<?php

class CalinDiacon_CmsSync_Adminhtml_Cms_BlockController extends Mage_Adminhtml_Controller_Action
{
    protected $_nodes = array();



    public function syncAction()
    {
        $params = $this->getRequest()->getParams();

        if(isset($params['block_id'])){

            // block id
            $blockId = $params['block_id'];

            $result = Mage::getModel('CalinDiacon_CmsSync_Model_Block')->sync($blockId);

            if($result['errors'])
                Mage::getSingleton('adminhtml/session')->addError($result['message']);

        }

        $this->_redirectReferer();
    }


}