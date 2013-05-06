<?php

class CalinDiacon_CmsSync_Model_Cms extends CalinDiacon_CmsSync_Model_Abstract
{

    public function syncStaticBlock($blockId)
    {
        /**
         * verify the source and get nodes info
         **/
        $validNodes = $this->getEnabledNodes();

        if($blockId && count($validNodes)){

            $this->blockId = $blockId;
            $modelBlock = Mage::getModel('cms/block');

            if(! $id = $modelBlock->load($blockId)){
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This block no longer exists!'));
                $this->_redirect('*/*');
                return;
            }

            $this->identifier = $modelBlock->getIdentifier();
            $this->storeIds = $modelBlock->getStoreId();
            $this->title = $modelBlock->getTitle();
            $this->content = $modelBlock->getContent();
            $this->updateTime = $modelBlock->getUpdateTime();

            foreach ($validNodes as $node) {

                $this->node = $node;
                $isNew = $this->checkForNewBlock();

                $isEnabled = $this->proxy->call($this->sessionId, 'cms_api.is_enabled');// 0 - disabled
                $source = $this->proxy->call($this->sessionId, 'cms_api.get_source');// 0 - node ; 1 - master

                if ($isEnabled && ! $source){
                    if ($isNew){
                        /**
                         * Create remote blocks
                         */
                        $createdResult = $this->proxy->call($this->sessionId, 'cms_api.block_create', array($modelBlock->getData()));

                    }else{

                        //@todo make the block an object and then compare
                        $remoteBlock = $this->proxy->call($this->sessionId, 'cms_api.block_info', $this->identifier);// array of info for the remote block

                        $isLater = $this->isLater($remoteBlock['update_time']);

                        if($isLater || $this->node->override){

                            $this->proxy->call($this->sessionId, 'cms_api.block_update', array($modelBlock->getData()));

                        }else{

                            Mage::log('skipping the remote because is newer :'  . $this->node->override);
                        }

                    }
                    /**
                     * save all media from the content
                     */
                    preg_match_all('/<img.*src=.*url="(.*)"\}\}.*\/\>/', $modelBlock->getContent(), $matches);
                    if (isset($matches[1])){
                        $allMedia = $matches[1];

                        foreach ($allMedia as $filename){
                            $fileData = base64_encode(file_get_contents(Mage::getBaseDir('media') . DS . $filename));
                            $data = array(
                                'fileName' => $filename,
                                'file' => $fileData
                            );

                            $this->proxy->call($this->sessionId, 'cms_api.block_media', array($data));

                        }


                    }
                }else{
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Remote node is disabled or is using different store!'));
                    //Mage::throwException('The remote node is not enabled stop furthe actions');
                }

            }

        }else{
            Mage::throwException('No valid nodes or invalid block.');
        }

    }

    /**
     * check the remote node block
     * and cancel if the node is disabled
     * @return bool
     */
    public function checkForNewBlock()
    {
        $this->prepareConnection();


        $new = $this->proxy->call($this->sessionId, 'cms_api.block_is_new', $this->identifier);
        if ($new){

            return true;
        }else{

            return false;
        }
    }

    /**
     * Connect to the current node
     * @return bool
     */
    protected function prepareConnection()
    {
        $this->proxy = new Zend_Soap_Client($this->node->getUrl());
        $this->sessionId = $this->proxy->login($this->node->getUsername(), $this->node->getPassword());
        if ($this->sessionId)
            return true;
        else
            return false;
    }




}