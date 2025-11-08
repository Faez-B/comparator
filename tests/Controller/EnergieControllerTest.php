<?php

namespace App\Test\Controller;

use App\Entity\Energie;
use App\Entity\User;
use App\Repository\EnergieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EnergieControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EnergieRepository $repository;
    private EntityManagerInterface $entityManager;
    private string $path = '/energie/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $doctrine = $container->get('doctrine');
        $this->entityManager = $doctrine->getManager();

        /** @var EnergieRepository $energieRepository */
        $energieRepository = $doctrine->getRepository(Energie::class);
        $this->repository = $energieRepository;

        // Nettoyer la base de données avant chaque test
        foreach ($this->repository->findAll() as $object) {
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

    public function testIndex(): void
    {
        // Créer quelques énergies de test
        $essence = new Energie();
        $essence->setNom('Essence');
        $this->entityManager->persist($essence);

        $diesel = new Energie();
        $diesel->setNom('Diesel');
        $this->entityManager->persist($diesel);

        $this->entityManager->flush();

        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Énergie');

        // Vérifier que les énergies sont affichées
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('Essence', $content);
        self::assertStringContainsString('Diesel', $content);
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Créer', [
            'energie[nom]' => 'Électrique',
        ]);

        self::assertResponseRedirects('/energie/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        // Vérifier que l'énergie a été créée avec le bon nom
        $energie = $this->repository->findOneBy(['nom' => 'Électrique']);
        self::assertNotNull($energie);
        self::assertSame('Électrique', $energie->getNom());
    }

    public function testShow(): void
    {
        $fixture = new Energie();
        $fixture->setNom('Hybride');

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Énergie');

        // Vérifier que le nom de l'énergie est affiché
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('Hybride', $content);
    }

    public function testEdit(): void
    {
        $fixture = new Energie();
        $fixture->setNom('GPL');

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Mettre à jour', [
            'energie[nom]' => 'GPL Updated',
        ]);

        self::assertResponseRedirects('/energie/');

        // Recharger l'énergie depuis la base de données
        $updatedEnergie = $this->repository->find($fixture->getId());

        self::assertNotNull($updatedEnergie);
        self::assertSame('GPL Updated', $updatedEnergie->getNom());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Energie();
        $fixture->setNom('Hydrogène');

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/energie/');
    }
}
