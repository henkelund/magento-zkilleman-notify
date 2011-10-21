<?php
/**
 * Zkilleman_Notify
 *
 * Copyright (C) 2011 Henrik Hedelund (henke.hedelund@gmail.com)
 *
 * This file is part of Zkilleman_Notify.
 *
 * Zkilleman_Notify is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zkilleman_Notify is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Zkilleman_Notify.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP Version 5.1
 *
 * @category  Zkilleman
 * @package   Zkilleman_Notify
 * @author    Henrik Hedelund <henke.hedelund@gmail.com>
 * @copyright 2011 Henrik Hedelund (henke.hedelund@gmail.com)
 * @license   http://www.gnu.org/licenses/lgpl.html GNU LGPL
 * @link      https://github.com/henkelund/magento-zkilleman-notify
 */

/**
 * Zkilleman_Notify_Block_Messages
 *
 * @category   Zkilleman
 * @package    Zkilleman_Notify
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
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