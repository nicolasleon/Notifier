<?php
/*************************************************************************************/
/*      This file is part of the Notifier Module                                   */
/*                                                                                   */
/*      Copyright (c) Omnitic                                                        */
/*      email : bonjour@omnitic.com                                                  */
/*      web : http://www.omnitic.com                                                 */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Notifier;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Module\BaseModule;

class Notifier extends BaseModule
{
    const EMAIL_MESSAGE_NAME = 'customer_account_creation';

    public function postActivation(ConnectionInterface $con = null)
    {
        if (null === MessageQuery::create()->findOneByName(self::EMAIL_MESSAGE_NAME)) {
            $message = new Message();

            $message
                ->setName(self::EMAIL_MESSAGE_NAME)

                ->setLocale('en_US')
                ->setTitle('Registration confirmation')
                ->setSubject("Your registration on {config key='store_name'} is confirmed")
                ->setHtmlMessage(file_get_contents(__DIR__ . DS . 'Config' . DS . 'email-contents.en.html'))
                ->setTextMessage(file_get_contents(__DIR__ . DS . 'Config' . DS . 'email-contents.en.txt'))
                ->setLocale('fr_FR')
                ->setTitle('Confirmation inscription')
                ->setSubject("Confirmation de votre inscription sur {config key='store_name'}")
                ->setHtmlMessage(file_get_contents(__DIR__ . DS . 'Config' . DS . 'email-contents.fr.html'))
                ->setTextMessage(file_get_contents(__DIR__ . DS . 'Config' . DS . 'email-contents.fr.txt'))
                ->save();
        }
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false)
    {
        // Delete message if required
        if ($deleteModuleData) {
            MessageQuery::create()->findOneByName(self::EMAIL_MESSAGE_NAME)->delete();
        }
    }
}
