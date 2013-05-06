<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 06/05/2013
 * Time: 15:52
 * 
 */
class CalinDiacon_CmsSync_Block_Adminhtml_Cms_Page_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{
    public function __construct()
    {
        parent::__construct();

        // show button only if source is enabled
        $cmsSyncEnabled = Mage::getStoreConfig(CalinDiacon_CmsSync_Model_Constants::CMSSYNC_ENABLE);
        if ($cmsSyncEnabled){
            $this->_addButton('syncall', array(
                'label' => Mage::helper('cmssync')->__('Sync Page'),
                'onclick' => 'setLocation(\''. $this->getUrl('cmssync/cms_page/sync') . 'page_id/' . $this->getRequest()->getParam('page_id') .  '\')',
            ),-1,5);
        }// end
    }

}