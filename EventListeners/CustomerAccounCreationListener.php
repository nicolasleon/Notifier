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

namespace Notifier\EventListeners;

use Notifier\Notifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;
use Thelia\Tools\URL;

class CustomerAccounCreationListener implements EventSubscriberInterface
{
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNotificationEmail(CustomerCreateOrUpdateEvent $event)
    {
        // Get the customer object from the event
        $customer = $event->getCustomer();

        // Send confirmation email
        $this->mailer->sendEmailToCustomer(
            Notifier::EMAIL_MESSAGE_NAME,
            $customer,
            [
                'customer_id' => $customer->getId()
            ]
        );
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::CUSTOMER_CREATEACCOUNT => ['sendNotificationEmail', 128]
        );
    }
}
