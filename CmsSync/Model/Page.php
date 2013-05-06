<?php
/**
 * Created by Calin Diacon.
 * User: cdiacon
 * Date: 05/05/2013
 * Time: 19:56
 * 
 */
class CalinDiacon_CmsSync_Model_Page extends Mage_Core_Model_Abstract
{
    public function sync($blockId)
    {
        $result = array('errors' => false, 'message' => "<br /><ul style='list-style-type: square;'>");
        // node models
        $nodes = $this->getValidNodes();

        if($nodes){

            $blockModel = Mage::getModel('cms/block')->load($blockId);

            // loop all nodes api
            foreach ($nodes as $node) {

                try{

                    $proxy = new SoapClient($node->getUrl());
                    $sessionId = $proxy->login($node->getUsername(), $node->getPassword());
                    $remoteBlock = $proxy->call($sessionId, 'cms_api.block_info', $blockModel->getIdentifier());// array of info for the remote block

                    if($remoteBlock){

                        // update block
                        $proxy->call($sessionId, 'cms_api.block_update', array($blockModel->getData()));
                    }else{

                        // create new block
                        $proxy->call($sessionId, 'cms_api.block_create', array($blockModel->getData()));
                    }
                    /**
                     * save all media from the content
                     */
                    preg_match_all('/<img.*src=.*url="(.*)"\}\}.*\/\>/', $blockModel->getContent(), $matches);
                    if (isset($matches[1])){
                        $allMedia = $matches[1];

                        foreach ($allMedia as $filename){
                            $fileData = base64_encode(file_get_contents(Mage::getBaseDir('media') . DS . $filename));
                            $data = array(
                                'fileName' => $filename,
                                'file' => $fileData
                            );

                            $proxy->call($sessionId, 'cms_api.block_media', array($data));

                        }


                    }

                }catch(Exception $e){
                    Mage::throwException($e->getMessage());
                    $result['errors'] = true;
                    $result['message'] = '<li>error sync remote node, ' . $e->getMessage() . '</li>';
                }
            }

        }else{
            Mage::getSingleton('adminhtml/session')->addNotice('No valid nodes found!');
            $result['errors'] = true;
            $result['message'] = '<li>No valid nodes found.</li>';
        }
        $result['message'] .='</ul>';
        return $result;

    }

}