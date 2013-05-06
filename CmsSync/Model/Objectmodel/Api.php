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
        $enabled = Mage::getStoreConfig(CalinDiacon_CmsSync_Model_Constants::CMSSYNC_ENABLE);
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
        if($this->isEnabled()){
            $modelBlock = Mage::getModel('cms/block')->load($identifier, 'identifier');

            if($modelBlock){

                return $modelBlock->getData();
            }
        }
        return false;
    }

    /**
     * get page info
     * @param $identifier
     * @return bool|mixed
     */
    public function getPageInfo($identifier)
    {
        if($this->isEnabled()){
            $pageModel = Mage::getModel('cms/page')->load($identifier, 'identifier');

            if($pageModel){

                return $pageModel->getData();
            }
        }
        return false;

    }

    /**
     * check if the block/page exists in remote
     * @param $identifier
     * @param string $model
     * @return bool
     */
    public function checkForNew($identifier, $model = 'page')
    {
        $blockModel = Mage::getModel('cms/' . $model)->load($identifier, 'identifier');

        if ($blockModel->getId()){

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
            if($this->checkForNew($data['identifier'], 'block')){

                $blockModel = Mage::getModel("cms/block");

                $blockModel->setData($staticBlock);
                try{

                    $result = $blockModel->save();
                    return $result;

                }catch(Exception $e){
                    Mage::logException($e);
                }
            }else{
                throw new Exception('this block already exists, not sav ng...'  . $data['identifier']);
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

    /**
     * create new page
     * @param $data
     * @return array
     */
    public function createPage($data)
    {
        $result = array('errors' => false, 'message' => '');
        $identifier = $data['identifier'];

        if (!empty($data)){

            if($this->checkForNew($identifier, 'page')){

                try{
                    $blockModel = Mage::getModel("cms/page");
                    $blockModel->setData($data);

                    $blockModel->save();

                }catch(Exception $e){
                    Mage::logException($e);
                    $result['errors'] = true;
                    $result['message'] = 'error saving the page model';
                }
            }else{
                $result['errors'] = true;
                $result['message'] = 'the page already exists ' . $data[$identifier];
            }
        }

        return $result;
    }

    /**
     * update page
     * @param $data
     * @return array|bool
     */
    public function updatePage($data)
    {
        $result = array('error' => false, 'message' => '');
        $identifier = $data['identifier'];


        $pageModel = Mage::getModel('cms/page')->load($identifier, 'identifier');

        try{


            if($pageModel){
                $pageModel->addData($data)->save();
                return true;
            }
        }catch(Exception $e){
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        return $result;

    }





}