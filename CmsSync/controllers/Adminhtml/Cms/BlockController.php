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

        $cmsModel = new CalinDiacon_CmsSync_Model_Cms();
        $cmsModel->syncStaticBlock($blockId);




    }


}