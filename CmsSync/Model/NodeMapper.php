<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 17/02/2013
 * Time: 14:34
 * 
 */
class CalinDiacon_CmsSync_Model_NodeMapper implements  Countable
{
    public $nodes = array();

    public function count()
    {
        return count($this->nodes);
    }
}