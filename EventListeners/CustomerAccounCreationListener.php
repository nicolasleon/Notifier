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

use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent; // Accès à l'évènement creation du compte client
use Thelia\Model\ConfigQuery; // Accès aux variable de configuration de la boutique

use Thelia\Mailer\MailerFactory;
use Thelia\Core\Event\TheliaEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;


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
        var_dump($event->type);
        // Set the email parameters

        // TOTO create config form for custom body content and / or template file
        $account_url = ConfigQuery::read("url_site") . '/login';
        $store_name = ConfigQuery::read("store_name");
        $body = <<<BDY
            <html>
                <body>
                    <p>
                        Bonjour,<br />
                        Votre inscription sur {$store_name} à bien été prise en compte.
                    </p>
                    <p>Vous pouvez accéder à votre espace client à cette adresse : {$account_url}</p>
                </body>
            </html>
BDY;
        $message = \Swift_Message::newInstance('[' . ConfigQuery::read("store_name") . '] Confirmation de votre inscription')
            ->addFrom(ConfigQuery::read("store_email"), ConfigQuery::read("store_name"))
            ->addReplyTo(ConfigQuery::read("store_email"), ConfigQuery::read("store_name"))
            ->addCc(ConfigQuery::read("store_email"), ConfigQuery::read('store_name'))
            ->addTo($customer->getEmail(), $customer->getFirstname() . ' ' . $customer->getLastname())
            ->setBody($body, 'text/html');

        // Send the email
        $this->mailer->send($message);
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::CUSTOMER_CREATEACCOUNT => ['sendNotificationEmail', 128]
        );
    }
}