<?php

class CalinDiacon_CmsSync_Block_Adminhtml_Cms_Block_Edit extends Mage_Adminhtml_Block_Cms_Block_Edit
{

    public function __construct()
    {
        parent::__construct();

        // show button only if source is enabled
        $cmsSyncEnabled = Mage::getStoreConfig(CalinDiacon_CmsSync_Model_Constants::CMSSYNC_ENABLE);
        if ($cmsSyncEnabled){
            $this->_addButton('syncall', array(
                'label' => Mage::helper('cmssync')->__('Sync Block'),
                'onclick' => 'setLocation(\''. $this->getUrl('cmssync/cms_block/sync') . 'block_id/' . $this->getRequest()->getParam('block_id') .  '\')',
            ),-1,5);
        }// end

    }
}