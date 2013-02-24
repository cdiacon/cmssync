<?php


class CalinDiacon_CmsSync_Model_System_Config_Source_Master
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('cmssync')->__('Master')),
            array('value' => 0, 'label' => Mage::helper('cmssync')->__('Node'))
        );

    }

    public function toArray()
    {
        return array(
            0 => Mage::helper('cmssync')->__('Node'),
            1 => Mage::helper('cmssync')->__('Master')
        );
    }

}