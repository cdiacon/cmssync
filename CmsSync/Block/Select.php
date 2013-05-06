<?php
/**
 * Created by Calin Diacon.
 * Html Select Element
 * User: cdiacon
 * Date: 06/05/2013
 * Time: 10:18
 * 
 */
class CalinDiacon_CmsSync_Block_Select extends Mage_Adminhtml_Block_Html_Select
{
    /**
     * return all in one line
     * @return string
     */
    public function _toHtml()
    {
        return trim(preg_replace('/s+/', ' ', parent::_toHtml()));

    }
}