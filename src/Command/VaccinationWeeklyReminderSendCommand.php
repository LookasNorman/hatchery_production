<?php

namespace App\Command;

use App\Repository\ContactInfoRepository;
use App\Repository\InputsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

class VaccinationWeeklyReminderSendCommand extends Command
{
    protected static $defaultName = 'app:vaccination-reminder:send';
    protected static $defaultDescription = 'Vaccination reminder';
    private $inputsRepository;
    private $contactInfoRepository;
    private $mailer;

    public function __construct(InputsRepository $inputsRepository, ContactInfoRepository $contactInfoRepository, MailerInterface $mailer)
    {
        parent::__construct(null);
        $this->inputsRepository = $inputsRepository;
        $this->contactInfoRepository = $contactInfoRepository;
        $this->mailer = $mailer;
    }


    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $contacts = $this->contactInfoRepository->findBy(['department' => 'Administrator']);

        $io->progressStart(count($contacts));
        $io->progressAdvance();
        $date = new \DateTime('midnight');
        $day = $date->format('D');
        if ($day === 'Mon') {
            $date->modify('-16 days');
        }
        $end = clone $date;
        $end->modify('+7 days');
        $inputs = $this->inputsRepository->vaccinationReminder($date, $end);

        if (count($inputs) > 0) {
            $email = (new TemplatedEmail())
                ->to('sbiesalski@zwdmalec.pl')
                ->addBcc('lkonieczny@zwdmalec.pl')
                ->subject('Ustalić szczepienie')
                ->htmlTemplate('emails/vaccinationReminder.html.twig')
                ->context([
                    'inputs' => $inputs
                ]);
            $this->mailer->send($email);
            $io->progressFinish();
            $io->success('Reminder email was send');
        }
        return 0;
    }
}
