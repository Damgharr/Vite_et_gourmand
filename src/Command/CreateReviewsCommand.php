<?php

namespace App\Command;

use App\Entity\Review;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-reviews', description: 'Create sample reviews for ROLE_USER users')]
class CreateReviewsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->em->getRepository(User::class)->findByRole('ROLE_USER');

        $texts = [
            'Moi et ma famille nous nous sommes régalés !',
            'Un vrai délice, je recommande fortement.',
            'Très bon rapport qualité-prix, livraison ponctuelle.',
            'Les plats étaient excellents et bien présentés.',
            'Une expérience gastronomique à renouveler.',
            'Le matériel était impeccable, tout était parfait.',
            'Nos invités ont adoré, merci beaucoup !',
            'Menu original et savoureux, nous avons adoré.',
            'Service impeccable et cuisine de qualité.',
            'Une soirée réussie grâce à Vite et Gourmand.',
        ];

        $notes = [3, 3, 4, 4, 4, 5, 5, 5, 5, 5];

        $index = 0;
        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $review = new Review();
                $review->setNote($notes[$index]);
                $review->setText($texts[$index]);
                $review->setStatus('acceptée');
                $review->setUser($user);
                $this->em->persist($review);
                $output->writeln("Review created for {$user->getEmail()} — {$notes[$index]}/5");
                $index++;
            }
        }

        $this->em->flush();
        $output->writeln('Done. 10 reviews created.');

        return Command::SUCCESS;
    }
}
