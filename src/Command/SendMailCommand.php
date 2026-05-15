<?php
# src/Command/SendMailCommand.php
# php bin/console app:send-mail

namespace App\Command;

use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Mime\Address;

#[AsCommand(name: 'app:send-mail')]
final class SendMailCommand
{
    public function __invoke(): int { // Available since Symfony 7.0. For earlier versions, use the execute() method instead.
        $email = (new MailtrapEmail())
            ->from(new Address('hello@demomailtrap.co', 'Mailtrap Test'))
            ->to(new Address('dg@kobra.rocks'))
            ->subject('You are awesome!')
            ->category('Integration Test')
            ->text('Congrats for sending test email with Mailtrap!')
        ;

        $response = MailtrapClient::initSendingEmails(
            apiKey: '0c59e12f343ee9c9b3e67cfbdb4ef058'
        )->send($email);

        var_dump(ResponseHelper::toArray($response));

        return Command::SUCCESS;
    }
}