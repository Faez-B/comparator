<?php

namespace App\Test\Controller;

use App\Entity\Marque;
use App\Entity\Energie;
use App\Entity\Voiture;
use App\Entity\User;
use App\Repository\VoitureRepository;
use App\Repository\MarqueRepository;
use App\Repository\EnergieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class VoitureControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private VoitureRepository $repository;
    private MarqueRepository $marqueRepository;
    private EnergieRepository $energieRepository;
    private EntityManagerInterface $entityManager;
    private string $path = '/voiture/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $doctrine = $container->get('doctrine');
        $this->entityManager = $doctrine->getManager();
        /** @var VoitureRepository $voitureRepository */
        $voitureRepository = $doctrine->getRepository(Voiture::class);
        $this->repository = $voitureRepository;
        /** @var MarqueRepository $marqueRepository */
        $marqueRepository = $doctrine->getRepository(Marque::class);
        $this->marqueRepository = $marqueRepository;
        /** @var EnergieRepository $energieRepository */
        $energieRepository = $doctrine->getRepository(Energie::class);
        $this->energieRepository = $energieRepository;

        // Nettoyer la base de données avant chaque test
        foreach ($this->repository->findAll() as $object) {
            $this->entityManager->remove($object);
        }
        foreach ($this->marqueRepository->findAll() as $object) {
            $this->entityManager->remove($object);
        }
        foreach ($this->energieRepository->findAll() as $object) {
            $this->entityManager->remove($object);
        }
        // Nettoyer aussi les utilisateurs
        $userRepository = $doctrine->getRepository(User::class);
        foreach ($userRepository->findAll() as $object) {
            $this->entityManager->remove($object);
        }
        $this->entityManager->flush();

        // Créer un utilisateur de test
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $testUser = new User();
        $testUser->setEmail('test@test.com');
        $testUser->setRoles(['ROLE_USER']);
        $testUser->setPassword($passwordHasher->hashPassword($testUser, 'test123'));

        $this->entityManager->persist($testUser);
        $this->entityManager->flush();

        // Simuler la connexion de l'utilisateur de test
        $this->client->loginUser($testUser);
    }

    /**
     * Crée une marque de test
     */
    private function createMarque(string $nom = 'Test Marque'): Marque
    {
        $marque = new Marque();
        $marque->setNom($nom);
        $this->entityManager->persist($marque);
        $this->entityManager->flush();

        return $marque;
    }

    /**
     * Crée une énergie de test
     */
    private function createEnergie(string $nom = 'Essence'): Energie
    {
        $energie = new Energie();
        $energie->setNom($nom);
        $this->entityManager->persist($energie);
        $this->entityManager->flush();

        return $energie;
    }

    /**
     * Crée une voiture de test complète
     */
    private function createVoiture(
        string $nom = 'Test Voiture',
        float $prix = 25000.0,
        ?Marque $marque = null,
        ?Energie $energie = null,
        string $etat = 'Neuf'
    ): Voiture {
        if ($marque === null) {
            $marque = $this->createMarque();
        }
        if ($energie === null) {
            $energie = $this->createEnergie();
        }

        $voiture = new Voiture();
        $voiture->setNom($nom);
        $voiture->setPrix($prix);
        $voiture->setMarque($marque);
        $voiture->setEnergie($energie);
        $voiture->setLastUpdate(new \DateTime());
        $voiture->setEtat($etat);
        $voiture->setConsommation(5.5);
        $voiture->setAnnee('2024');
        $voiture->setKilometrage('10000');

        $this->entityManager->persist($voiture);
        $this->entityManager->flush();

        return $voiture;
    }

    public function testIndex(): void
    {
        // Créer quelques voitures de test
        $this->createVoiture('Renault Clio', 20000.0);
        $this->createVoiture('Peugeot 308', 30000.0);

        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Voiture');

        // Vérifier que les voitures sont affichées
        self::assertStringContainsString('Renault Clio', $this->client->getResponse()->getContent());
        self::assertStringContainsString('Peugeot 308', $this->client->getResponse()->getContent());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        // Créer les entités nécessaires pour le formulaire
        $marque = $this->createMarque('Toyota');
        $energie = $this->createEnergie('Hybride');

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Créer', [
            'voiture[nom]' => 'Toyota Prius',
            'voiture[prix]' => 28000.0,
            'voiture[marque]' => $marque->getId(),
            'voiture[energie]' => $energie->getId(),
            'voiture[etat]' => 'Neuf',
            'voiture[consommation]' => 4.5,
            'voiture[annee]' => 2024,
            'voiture[kilometrage]' => 0,
        ]);

        self::assertResponseRedirects('/voiture/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        // Vérifier que la voiture a été créée avec les bonnes valeurs
        $voiture = $this->repository->findOneBy(['nom' => 'Toyota Prius']);
        self::assertNotNull($voiture);
        self::assertSame('Toyota Prius', $voiture->getNom());
        self::assertSame(28000.0, $voiture->getPrix());
        self::assertSame('Toyota', $voiture->getMarque()->getNom());
        self::assertSame('Hybride', $voiture->getEnergie()->getNom());
    }

    public function testShow(): void
    {
        $marque = $this->createMarque('BMW');
        $energie = $this->createEnergie('Diesel');

        $fixture = $this->createVoiture(
            nom: 'BMW Serie 3',
            prix: 45000.0,
            marque: $marque,
            energie: $energie
        );

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Voiture');

        // Vérifier que les informations de la voiture sont affichées
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('BMW Serie 3', $content);
        self::assertStringContainsString('45000', $content);
    }

    public function testEdit(): void
    {
        $marque = $this->createMarque('Volkswagen');
        $nouvelleMarque = $this->createMarque('Audi');
        $energie = $this->createEnergie('Essence');
        $nouvelleEnergie = $this->createEnergie('Électrique');

        $fixture = $this->createVoiture(
            nom: 'VW Golf',
            prix: 28000.0,
            marque: $marque,
            energie: $energie
        );

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Mettre à jour', [
            'voiture[nom]' => 'Audi A3 Updated',
            'voiture[prix]' => 35000.0,
            'voiture[marque]' => $nouvelleMarque->getId(),
            'voiture[energie]' => $nouvelleEnergie->getId(),
            'voiture[etat]' => 'Occasion',
            'voiture[consommation]' => 6.5,
            'voiture[annee]' => 2023,
            'voiture[kilometrage]' => 15000,
        ]);

        self::assertResponseRedirects('/voiture/');

        // Recharger la voiture depuis la base de données
        $updatedVoiture = $this->repository->find($fixture->getId());

        self::assertNotNull($updatedVoiture);
        self::assertSame('Audi A3 Updated', $updatedVoiture->getNom());
        self::assertSame(35000.0, $updatedVoiture->getPrix());
        self::assertSame('Audi', $updatedVoiture->getMarque()->getNom());
        self::assertSame('Électrique', $updatedVoiture->getEnergie()->getNom());
        self::assertSame('Occasion', $updatedVoiture->getEtat());
        self::assertSame(6.5, $updatedVoiture->getConsommation());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $marque = $this->createMarque('Ford');
        $energie = $this->createEnergie('Diesel');

        $fixture = $this->createVoiture(
            nom: 'Ford Focus',
            prix: 22000.0,
            marque: $marque,
            energie: $energie
        );

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        // Le formulaire de suppression utilise une méthode POST avec un token CSRF
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/voiture/');
    }

    public function testSearchWithFilters(): void
    {
        // Créer des voitures avec différentes caractéristiques
        $marqueRenault = $this->createMarque('Renault');
        $marquePeugeot = $this->createMarque('Peugeot');
        $energieEssence = $this->createEnergie('Essence');
        $energieDiesel = $this->createEnergie('Diesel');

        $this->createVoiture('Renault Clio', 18000.0, $marqueRenault, $energieEssence, 'Neuf');
        $this->createVoiture('Peugeot 308', 25000.0, $marquePeugeot, $energieDiesel, 'Neuf');
        $this->createVoiture('Renault Megane', 22000.0, $marqueRenault, $energieEssence, 'Occasion');

        // Test de recherche par marque
        $this->client->request('POST', '/voiture/search', [
            'marque' => $marqueRenault->getId(),
        ]);

        self::assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();

        // Vérifier que seules les Renault sont retournées
        self::assertStringContainsString('Renault Clio', $content);
        self::assertStringContainsString('Renault Megane', $content);
        self::assertStringNotContainsString('Peugeot 308', $content);
    }

    public function testSearchWithMaxPrice(): void
    {
        $marque = $this->createMarque('Citroën');
        $energie = $this->createEnergie('Essence');

        $this->createVoiture('Citroën C3', 15000.0, $marque, $energie);
        $this->createVoiture('Citroën C4', 20000.0, $marque, $energie);
        $this->createVoiture('Citroën C5', 30000.0, $marque, $energie);

        // Test de recherche avec prix maximum
        $this->client->request('POST', '/voiture/search', [
            'prixMax' => 21000,
        ]);

        self::assertResponseStatusCodeSame(200);
        $content = $this->client->getResponse()->getContent();

        // Vérifier que seules les voitures dans le budget sont retournées
        self::assertStringContainsString('Citroën C3', $content);
        self::assertStringContainsString('Citroën C4', $content);
        self::assertStringNotContainsString('Citroën C5', $content);
    }
}
