<?php

class CalinDiacon_CmsSync_Model_NodeMapper implements  Countable
{
    public $nodes = array();

    public function count()
    {
        return count($this->nodes);
    }
}