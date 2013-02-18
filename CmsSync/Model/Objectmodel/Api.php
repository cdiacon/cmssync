<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 27/01/2013
 * Time: 22:12
 * 
 */
class CalinDiacon_CmsSync_Model_ObjectModel_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * test action
     * @param $args
     * @return string
     */
    public function info($args)
    {
        return '1 from the custom option info action from api  ' . $args;
    }
    public function isEnabled()
    {
        $enabled = Mage::getStoreConfig('cmssync/general/enabled');
        return $enabled;
    }
    public function getSource()
    {
        return Mage::getStoreConfig('cmssync/general/source');
    }

    public function getBlockInfo($blockId)
    {
        $modelBlock = Mage::getModel('cms/block')->load($blockId);

        return $modelBlock;


    }
    public function isNew($blockId)
    {
        $modelBlock = Mage::getModel('cms/block');

        if ($modelBlock->load($blockId))
            return true;
        else
            return false;

    }


}