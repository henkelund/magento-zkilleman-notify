<?php

class Zkilleman_Notify_Block_Messages extends Mage_Core_Block_Messages
{
    public function getGroupedHtml()
    {
        if (!Mage::getStoreConfig('notify/general/enabled')) {
            return parent::getGroupedHtml();
        }
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );
        $jsonMessages = array();
        foreach ($types as $type) {
            if ( $messages = $this->getMessages($type) ) {

                foreach ( $messages as $message ) {
                    $jsonMessages[] = array(
                        'type' => $type,
                        'text' => ($this->_escapeMessageFlag) ?
                            $this->htmlEscape($message->getText()) :
                            $message->getText()
                    );
                }
            }
        }
        return sprintf('
            <script type="text/javascript">
                //<![CDATA[
                Event.observe(document, \'dom:loaded\', function() {
                    var _groupedMessages = %s;
                    for (var i = 0; i < _groupedMessages.length; ++i) {
                        _zkillemanNotify.addMessage(_groupedMessages[i]);
                    }
                });
                //]]>
            </script>', 
            Mage::helper('core')->jsonEncode($jsonMessages));
    }
}