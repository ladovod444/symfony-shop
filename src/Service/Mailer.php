<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
//use Swift_Mailer;
//use Swift_Message;
//use Twig_Environment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class Mailer
{
    // Пока сделать константой, потом вывести в .env
    public const FROM_ADDRESS = 'ladovod77@yandex.ru';


    /**
     * @var Environment
     */
    //private $twig;

    public function __construct(
       //private Swift_Mailer $mailer,
       private MailerInterface $mailer,
       //private Environment $twig

    )  {

    }

    /**
     * @param User $user
     *
     * @throws TransportExceptionInterface
     */
    public function sendConfirmationMessage(User $user)
    {
        $email = (new TemplatedEmail())
            ->from(self::FROM_ADDRESS)
            ->to(new Address('ladovod@gmail.com'))
            ->subject('Thanks for signing up!')

            // path of the Twig template to render
            ->htmlTemplate('security/confirmation.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'user' => $user
            ]);
        //$this->mailer->
        $this->mailer->send($email);
//        $messageBody = $this->twig->render('security/confirmation.html.twig', [
//            'user' => $user
//        ]);
//
//        $message = new Swift_Message();
//        $message
//            ->setSubject('Вы успешно прошли регистрацию!')
//            ->setFrom(self::FROM_ADDRESS)
//            ->setTo($user->getEmail())
//            ->setBody($messageBody, 'text/html');
//
//        return $this->mailer->send($message);
    }
}