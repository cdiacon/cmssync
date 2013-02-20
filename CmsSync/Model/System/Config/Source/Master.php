<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 27/01/2013
 * Time: 19:33
 * 
 */

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