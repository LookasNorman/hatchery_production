<?php

namespace App\Command;

use App\Repository\ContactInfoRepository;
use App\Repository\InputsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

class NextInputReminderSendCommand extends Command
{
    protected static $defaultName = 'app:next-input-reminder:send';
    protected static $defaultDescription = 'Input reminder';
    private $contactInfoRepository;
    private $inputsRepository;
    private $mailer;

    public function __construct(ContactInfoRepository $contactInfoRepository, InputsRepository $inputsRepository, MailerInterface $mailer)
    {
        parent::__construct(null);
        $this->contactInfoRepository = $contactInfoRepository;
        $this->inputsRepository = $inputsRepository;
        $this->mailer = $mailer;
    }


    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $contacts = $this->contactInfoRepository->findBy(['department' => 'Administrator']);

        $io->progressStart(count($contacts));
            $io->progressAdvance();
            $inputs = $this->inputsRepository->inputsReminder();

            $email = (new TemplatedEmail())
                ->to('kkrakowiak@zwdmalec.pl')
                ->addTo('rgolec@zwdmalec.pl')
                ->addBcc('lkonieczny@zwdmalec.pl')
                ->subject('Przypomnienie o potencjalnych nakładach')
                ->htmlTemplate('emails/inputReminder.html.twig')
                ->context([
                    'inputs' => $inputs
                ]);
            $this->mailer->send($email);
        $io->progressFinish();

        $io->success('Reminder email was send');
        return 0;
    }
}
