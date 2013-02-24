<?php

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

        $cmsModel = new CalinDiacon_CmsSync_Model_Cms();
        $cmsModel->syncStaticBlock($blockId);

        Mage::getSingleton('adminhtml/session')->addSuccess('All nodes was successfull updated.');

        $this->_redirect('*/cms_block/index');

    }


}