<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 17/02/2013
 * Time: 18:35
 * 
 */
class CalinDiacon_CmsSync_Model_Block
{
    public $blockId;
    public $title;
    public $identifier;
    public $content;
    protected  $_creation_time;
    protected  $_update_time;
    public $is_active;
    protected $_storeId;
    protected $_stores = array();
    public $storeCodes = array();


}