<?php

namespace GestionDuStock\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GestionDuStock\Repository\StockMagasinRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class DateExpirationCommand extends Command
{
    protected static $defaultName = 'gestiondustock:dateexpiration';

    private $stockMagasinRepository;

    public function __construct(StockMagasinRepository $stockMagasinRepository)
    {
        $this->stockMagasinRepository = $stockMagasinRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Ce commande envoie un e-mail lorsque la date d\'expiration d\'un produit est atteinte.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Démarrage de la tâche planifiée...</info>");

        $expiringStock = $this->stockMagasinRepository->findExpiringStock();

        if (!empty($expiringStock)) {
            $adminEmail = 'badii.abdelkhalak@esprit.tn';
            $subject = 'Alerte : Produits expirant bientôt';
            $message = 'Certains produits dans le stock magasin vont expirer bientôt.';
            
            // Envoi de l'e-mail en utilisant la fonction mail() de PHP
            $mailSent = mail($adminEmail, $subject, $message);

            if ($mailSent) {
                $output->writeln("<info>E-mail envoyé à l'administrateur pour les produits expirant bientôt.</info>");
            } else {
                $output->writeln("<error>Erreur lors de l'envoi de l'e-mail.</error>");
            }
        } else {
            $output->writeln("<info>Aucun produit n'expire dans les 3 prochains jours.</info>");
        }

        $output->writeln("<info>Tâche planifiée terminée.</info>");

        return Command::SUCCESS;
    }
}
