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
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cd_cmssync')->__('This block no longer exists!'));
                $this->_redirect('*/*');
                return;
            }

            $identifier = $model->getIdentifier();
            $title = $model->getTitle();
            $content = $model->getContent();



        }



    }


}