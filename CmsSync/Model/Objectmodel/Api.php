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

}