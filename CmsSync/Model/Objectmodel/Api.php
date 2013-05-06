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
     * Info
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

    /**
     * get an array of block info
     * @param $identifier
     * @return array
     */
    public function getBlockInfo($identifier)
    {
        $modelBlock = Mage::getModel('cms/block')->load($identifier, 'identifier');

        if($modelBlock){

            return $modelBlock->getData();
        }
        return false;
    }

    /**
     * check if the block exists in remote
     * @param $identifier string
     * @return mixed
     */
    public function checkForNewBlock($identifier)
    {
        $blockModel = Mage::getModel('cms/block')->load($identifier, 'identifier');

        if ($blockModel->getBlockId()){

            return false;
        }
        return true;
    }

    /**
     * Create new block
     * @param $data
     * @return bool|Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function createBlock($data)
    {
        if (!empty($data)){

            $staticBlock = array(
                'title' => $data['title'],
                'identifier' => $data['identifier'],
                'content' => $data['content'],
                'is_active' => $data['is_active'],
                'store_id' => $data['store_id'],
                'stores' => $data['stores']
            );
            if($this->checkForNewBlock($data['identifier'])){

                $blockModel = Mage::getModel("cms/block");

                $blockModel->setData($staticBlock);
                try{

                    $result = $blockModel->save();
                    return $result;

                }catch(Exception $e){
                    Mage::logException($e);
                }
            }else{
                throw new Exception('this block already exists, not saving...'  . $data['identifier']);
            }
        }

        return false;
    }

    /**
     * Update static block
     * @param $data
     * @return $this|bool
     */
    public function updateBlock($data)
    {
        $identifier = $data['identifier'];

        $staticBlock = array(
            'title' => $data['title'],
            'content' => $data['content'],
            'is_active' => $data['is_active']
        );

        $blockModel = Mage::getModel('cms/block')->load($identifier, 'identifier');

        if($blockModel){
            $blockModel->addData($staticBlock)->save();
            return true;
        }
        return false;

    }

    /**
     * @param $data
     */
    public function createMedia($data)
    {
        $fileName = $data['fileName'];
        $file = base64_decode($data['file']);
        $filePath = Mage::getBaseDir('media') . DS . $fileName;

        if(! file_exists($filePath)){

            try{

                file_put_contents($filePath, $file);
            }catch (Exception $e){
                Mage::logException($e);
            }
        }
    }



}