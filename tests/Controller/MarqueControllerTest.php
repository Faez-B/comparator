<?php

namespace App\Test\Controller;

use App\Entity\Marque;
use App\Entity\User;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MarqueControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MarqueRepository $repository;
    private EntityManagerInterface $entityManager;
    private string $path = '/marque/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $doctrine = $container->get('doctrine');
        $this->entityManager = $doctrine->getManager();

        /** @var MarqueRepository $marqueRepository */
        $marqueRepository = $doctrine->getRepository(Marque::class);
        $this->repository = $marqueRepository;

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
        // Créer quelques marques de test
        $renault = new Marque();
        $renault->setNom('Renault');
        $this->entityManager->persist($renault);

        $peugeot = new Marque();
        $peugeot->setNom('Peugeot');
        $this->entityManager->persist($peugeot);

        $this->entityManager->flush();

        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Marque');

        // Vérifier que les marques sont affichées
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('Renault', $content);
        self::assertStringContainsString('Peugeot', $content);
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Créer', [
            'marque[nom]' => 'Tesla',
        ]);

        self::assertResponseRedirects('/marque/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        // Vérifier que la marque a été créée avec le bon nom
        $marque = $this->repository->findOneBy(['nom' => 'Tesla']);
        self::assertNotNull($marque);
        self::assertSame('Tesla', $marque->getNom());
    }

    public function testShow(): void
    {
        $fixture = new Marque();
        $fixture->setNom('BMW');

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Marque');

        // Vérifier que le nom de la marque est affiché
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('BMW', $content);
    }

    public function testEdit(): void
    {
        $fixture = new Marque();
        $fixture->setNom('Audi');

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Mettre à jour', [
            'marque[nom]' => 'Audi Updated',
        ]);

        self::assertResponseRedirects('/marque/');

        // Recharger la marque depuis la base de données
        $updatedMarque = $this->repository->find($fixture->getId());

        self::assertNotNull($updatedMarque);
        self::assertSame('Audi Updated', $updatedMarque->getNom());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Marque();
        $fixture->setNom('Volkswagen');

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/marque/');
    }
}
