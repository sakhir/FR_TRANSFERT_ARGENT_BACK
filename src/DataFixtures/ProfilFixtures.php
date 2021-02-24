<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfilFixtures extends Fixture 

{

    public const ADMIN_PROFIL_REFERENCE = 'Super-Admin';
    public const ADMIN_AGENCE_PROFIL_REFERENCE = 'Admin-Partenaire';
    public const CAISSIER_PROFIL_REFERENCE = 'Caissier';
    public const USER_PROFIL_REFERENCE = 'Utilisateur';
 


    public function load(ObjectManager $manager)
{
        $Admin = new Profil();
        $Admin->setLibelle(self::ADMIN_PROFIL_REFERENCE);
        $Admin->setArchivage(0);
        $manager->persist($Admin);
        // $AdminP = new Profil();
        // $AdminP->setLibelle(self::ADMIN_AGENCE_PROFIL_REFERENCE);
        // $manager->persist($AdminP);
        
        // $Caissier = new Profil();
        // $Caissier->setLibelle(self::CAISSIER_PROFIL_REFERENCE);
        // $manager->persist($Caissier);

        // $User = new Profil();
        // $User->setLibelle(self::USER_PROFIL_REFERENCE);
        // $manager->persist($User);
        
        

        $this->addReference(self::ADMIN_PROFIL_REFERENCE, $Admin);
        // $this->addReference(self::ADMIN_AGENCE_PROFIL_REFERENCE, $AdminP);
        // $this->addReference(self::CAISSIER_PROFIL_REFERENCE, $Caissier);
        // $this->addReference(self::USER_PROFIL_REFERENCE, $User);
        
         $manager->flush();    
  }
}  