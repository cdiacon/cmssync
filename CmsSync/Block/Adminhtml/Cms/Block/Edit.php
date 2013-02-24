<?php

class CalinDiacon_CmsSync_Block_Adminhtml_Cms_Block_Edit extends Mage_Adminhtml_Block_Cms_Block_Edit
{

    public function __construct()
    {
        $this->_objectId = 'block_id';
        $this->_controller = 'cms_block';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('cms')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('cms')->__('Delete Block'));

        $isMaster = Mage::getStoreConfig('cmssync/general/source');
        if ($isMaster){
            $this->_addButton('sync', array(
                'label' => Mage::helper('adminhtml')->__('Sync Block to all nodes'),
                'onclick' => 'setLocation(\''. $this->getUrl('*/*/sync') . 'block_id/' . $this->getRequest()->getParam('block_id') .  '\')',
                'class' => 'sync'
            ),-1,5);
        }


        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('cms_block')->getId()) {
            return Mage::helper('cms')->__("Edit Block '%s'", self::escapeHtml(Mage::registry('cms_block')->getTitle()));
        }
        else {
            return Mage::helper('cms')->__('New Block');
        }
    }

}