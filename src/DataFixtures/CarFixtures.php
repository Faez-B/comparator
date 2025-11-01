<?php

namespace App\DataFixtures;

use App\Entity\Energie;
use App\Entity\Marque;
use App\Entity\Voiture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $energies = [];
        foreach (['Essence', 'Diesel', 'Électrique', 'Hybride'] as $name) {
            $energie = new Energie();
            $energie->setNom($name);
            $manager->persist($energie);
            $energies[] = $energie;
        }

        $marques = [];
        foreach (['Peugeot', 'Renault', 'Citroën', 'Volkswagen', 'BMW', 'Mercedes', 'Audi', 'Ford', 'Opel', 'Fiat'] as $name) {
            $marque = new Marque();
            $marque->setNom($name);
            $manager->persist($marque);
            $marques[] = $marque;
        }

        for ($i = 0; $i < 50; $i++) {
            $voiture = new Voiture();
            $voiture->setNom('Voiture ' . $i);
            $voiture->setPrix(mt_rand(10000, 50000));
            $voiture->setAnnee(mt_rand(2010, 2023));
            $voiture->setKilometrage(mt_rand(0, 200000));
            $voiture->setConsommation(mt_rand(4, 10));
            $voiture->setEtat($i % 2 === 0 ? 'Neuf' : 'Occasion');
            $voiture->setMarque($marques[array_rand($marques)]);
            $voiture->setEnergie($energies[array_rand($energies)]);
            $voiture->setLastUpdate(new \DateTime());
            $manager->persist($voiture);
        }

        $manager->flush();
    }
}
