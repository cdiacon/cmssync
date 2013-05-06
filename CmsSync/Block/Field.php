<?php
/**
 * Created by Calin Diacon.
 * System configuration array input
 * User: cdiacon
 * Date: 06/05/2013
 * Time: 09:59
 * 
 */
class CalinDiacon_CmsSync_Block_Field extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Get select block for type
     * @return mixed
     */
    protected function _getTypeRenderer()
    {
        if (!$this->_typeRenderer) {
            $this->_typeRenderer = $this->getLayout()
                ->createBlock('cmssync/select')
                ->setIsRenderToJsTemplate(true);
        }
        return $this->_typeRenderer;
    }

    /**
     * Get select block for search field
     * @return mixed
     */
    protected function _getSearchFieldRenderer()
    {
        if (!$this->_searchFieldRenderer) {
            $this->_searchFieldRenderer = $this->getLayout()
                ->createBlock('cmssync/select')
                ->setIsRenderToJsTemplate(true);
        }
        return $this->_searchFieldRenderer;
    }


    protected function _prepareToRender()
    {

        $this->_typeRenderer = null;
        $this->_searchFieldRenderer = null;

        $this->addColumn('id', array(
            'label' => Mage::helper('cmssync')->__('ID'),
            'class' => 'disabled',
            'style' => 'width:20px;'
        ));
        $this->addColumn('apiurl', array(
            'label' => Mage::helper('cmssync')->__('API Url'),
            'style' => 'width:400px '

        ));
        $this->addColumn('username', array(
            'label' => Mage::helper('cmssync')->__('Username'),
            'style' => 'width: 150px'
        ));
        $this->addColumn('password', array(
            'label' => Mage::helper('cmssync')->__('Api key'),
            'style' => 'width: 150px'
        ));

        // Disables "Add after" button
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('cmssync')->__('Add Field');
    }

}