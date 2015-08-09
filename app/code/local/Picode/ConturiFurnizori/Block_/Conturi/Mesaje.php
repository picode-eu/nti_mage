<?php
class Picode_ConturiFurnizori_Block_Conturi_Mesaje extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function getReadAction($messageId)
    {
        return Mage::getUrl('conturifurnizori/mesaje/detalii/id/' . $messageId , array('_secure' => true));
    }
    
    public function getReplyAction()
    {
        return Mage::getUrl('conturifurnizori/mesaje/replaytomessage/' , array('_secure' => true));
    }
    
    public function getAllMessages()
    {
        $messages = Mage::getModel('conturifurnizori/usermessage')->getCollection()
            ->addFieldToFilter(array('recever_id', 'sender_id'),
                    array(
                        array('recever_id', 'eq' => $this->getCustomer()->getId()),
                        array('sender_id',  'eq' => $this->getCustomer()->getId())
                    )
                )
            ->setOrder('message_id','desc');
                              
        if ($messages) {
            return $messages;
        }
        
        return false;
    }
    
    public function getUnreadMessages()
    {
        $messages = $this->getAllMessages()
            ->addFieldToFilter('is_deleted', 0)
            ->addFieldToFilter('is_read', 0)
            ->addFieldToFilter('recever_id', $this->getCustomer()->getId());
                
        return $messages;
    }
    
    public function getSenderFullName($senderId, $replayTo)
    {
        $sender = Mage::getModel('customer/customer')->load($senderId);
        
        if ($sender->getId()) {
            return $sender->getFirstname() . ' ' . $sender->getLastname();
        } else {
            return $replayTo;
        }
        
        return;
    }

    public function getFirstMessages()
    {
        $messages = $this->getAllMessages()
            ->addFieldToFilter('is_deleted', 0)
            ->addFieldToFilter('is_first', 1);
            
        return $messages;
    }

    public function isConversation($messageId)
    {
        $messages = $this->getAllMessages()
            ->addFieldToFilter('is_deleted', 0)
            ->addFieldToFilter('first_message_id', $messageId);
            
        //Zend_Debug::dump($messages->getSize());
            
        if ($messages->getSize()) {
            return true;
        }
        
        return false;
    }
    
    public function getLastMessageFromConversation($messageId)
    {
        $messages = $this->getAllMessages()
            ->addFieldToFilter('is_deleted', 0)
            ->addFieldToFilter('first_message_id', $messageId);
                
        foreach ($messages as $message) {
            if ($this->getCustomer()->getId() == $message->getReceverId() && !$message->getIsRead()) {
                return false;
            } else {
                /**
                 * because the messages collection is already ordered as descending
                 * we have to get the first item from collection
                 */
                return $messages->getFirstItem();
            }
        }
        
        return;
    }
    
    public function countConversation($firstMessageId)
    {
        $conversation = $this->getAllMessages()
            ->addFieldToFilter('is_deleted', 0)
            ->addFieldToFilter(array('message_id', 'first_message_id'),
                    array(
                        array('message_id',       'eq' => $firstMessageId),
                        array('first_message_id', 'eq' => $firstMessageId)
                    )
                );
            
        if ($conversation->getSize()) {
            return $conversation->getSize();
        }
        
        return false;
    }
    
    public function getConversation($messageId)
    {
        $conversation = Mage::getModel('conturifurnizori/usermessage')->getCollection()
                ->addFieldToFilter(array('message_id', 'first_message_id'),
                    array(
                        array('message_id',       'eq' => $messageId),
                        array('first_message_id', 'eq' => $messageId)
                    )
                );
                
        return $conversation;
    }
    
    public function getMessageDetails()
    {
        $messageId = $this->getRequest()->getParam('id');
        
        if ($messageId) {
            $messageDetails = Mage::getModel('conturifurnizori/usermessage')->load($messageId);
            
            if ($messageDetails->getId()) {
                // set message as read
                if ($this->getCustomer()->getId() == $messageDetails->getReceverId()) {
                    $messageDetails->setIsRead(1)->save();
                }
                
                if ($messageDetails->getIsFirst()) {
                    $firstMessageId = $messageDetails->getId();
                } else {
                    $firstMessageId = $messageDetails->getFirstMessageId();
                }
                
                Mage::getSingleton('customer/session')->setReplyTo($firstMessageId);
                
                return $messageDetails;
            }
        }
        
        return;
    }
	
	public function getSentMessages()
	{
		$messages = $this->getAllMessages()
            ->addFieldToFilter('is_deleted', 0)
			->addFieldToFilter('sender_id', $this->getCustomer()->getId())
            ->addFieldToFilter('is_first', 1);
            
        return $messages;
	}
    
    public function getReceiverEmail($messageId)
    {
        $messageDetails = Mage::getModel('conturifurnizori/usermessage')->load($messageId);
        return $messageDetails->getReplayTo();
    }
    
}


















