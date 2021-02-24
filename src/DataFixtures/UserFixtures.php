<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Commission;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $encode;
    
    public function __construct(UserPasswordEncoderInterface $encode)
    {
        $this->encode = $encode;
      
    } 
  

    public function load(ObjectManager $manager)
    {

        $borneInf=array(1,5001,10001,15001,20001,50001,60001,75001,120001,150001,200001,250001,300001,400001,750001,900001,1000001,1125001,14000001,2000001);
        $borneSup=array(5000,10000,15000,20000,50000,60000,75000,120000,150000,200000,250000,300000,400000,750000,900000,1000000,1125000,14000000,20000000,30000000);
        $valeur=array(425,850,1270,1695,2500,3000,4000,5000,6000,7000,8000,9000,12000,15000,22000,25000,27000,30000,30000,0.2);
        
        for($i=0;$i<count($borneInf);$i++){
            $tarif=new Commission();
            $tarif->setBorneInferieur($borneInf[$i]);
            $tarif->setBorneSuperieur($borneSup[$i]);
            $tarif->setValeur($valeur[$i]);
            $manager->persist($tarif);
        }



                 
            $admin = new User;           
            $admin ->setEmail("sakhir@gmail.com");
            $admin ->setPrenom("Sakhir");
            $admin ->setNom("Fall");
            $admin->setUsername("ahmadou");
            $admin->setAdresse("Fass");
            $admin->setTelephone("772432526");
            $admin->setStatut(false);
            $admin ->setProfil($this->getReference(ProfilFixtures::ADMIN_PROFIL_REFERENCE));
            $password = $this->encode->encodePassword ($admin, 'passer');           
            $admin ->setPassword($password);  

            $manager->persist($admin);  

       
        
      
        $manager->flush();   
    }

    
}