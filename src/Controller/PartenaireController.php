<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Repository\PartenaireRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PartenaireController extends AbstractController


{
    private $userRepo;
    private $serializer;
    private $security;

    public function __construct(EntityManagerInterface $manager   ,PartenaireRepository $part ,SerializerInterface $serializer ,Security $security ,UserRepository $userRepo)
    {
        $this->manager = $manager;
        $this->part=$part ;
        $this->serializer=$serializer;
        $this->security = $security;
        $this->userRepo = $userRepo;

    }
      // creeons une fonction addPartenaireEtCompte qui va creer un partenaire et le  compte associé  à ce partenaire  
    
      public function addPartenaireEtCompte(Request $request  ) {
         
        //$userpost =$request->request->all();
        $data=json_decode($request->getContent(),true);
       
        $agencepart=$this->serializer->denormalize($data ,Partenaire::class);
     
        $agencepart->setDateCreation(new \DateTime());
        $agencepart->setStatut(false);
       //nous allons generer un  numero de compte vu qu on doit creer en meme temps le compte
       $numCompte=substr($agencepart->getNinea(),0,4).substr($agencepart->getLocalisation(),0,2).''.rand(0,10000);
      

       $compte =(new Compte)
       ->setNumeroCompte($numCompte)
       ->setSolde(0)
       ->setCodeBank(5166616)
       ->setNomBeneficiaire($agencepart->getNomPartenaire())
       ->addUser($this->security->getUser());
       $agencepart->setCompte($compte);
       $this->manager->persist($compte);
       $this->manager->persist($agencepart);
     
       $this->manager->flush();
    
       return $this->json('Partenaire '.$agencepart->getNomPartenaire().'  bien ajout\'é avec le compte '.$numCompte,Response::HTTP_OK); 

      
   }


     // Dans cette fonction nous allons attribuer pour une agence des utilisateurs 

     public function AttibuerUsersAgence(Request $request ,$id)
    {
        $json = json_decode($request->getContent());
        $partenaire=$this->part->find($id);
         
         // verifions si l'agence en question existe dabord 
         if($partenaire) {
             $tab=$json->id;
           
             //verifions si le tableau n est pas vide 
             if(count($tab)>0){
                 for ($i=0; $i < count($tab); $i++) { 
                     $idUser=$tab[$i];
                     $user=$this->userRepo->find($idUser);
                     if($user && $user->getProfil()->getLibelle()=="Partenaire") {
                     //a partir de la l utilisateur existe et c est un partenaire
                     //verifions s'il n est pas deja lié à une agence  
                       if($user->getPartenaire()==null) {
                         // c est bel et bien un utilisateur qui n a pas encore d'agence 
                         //je recuperer ici l agence 
                         
                           $user->setPartenaire($partenaire);


                       }
                      
                    }else {
                        return $this->json('l\'utilisateur n existe pas ou bien c est pas un partenaire   ',Response::HTTP_OK);  
                    }
                 }

             }
         }
         else {
            return $this->json('Choisissez une agence svp  ',Response::HTTP_OK);  
         }
         
       $this->manager->flush();
       return $this->json('Utilisateur(s) bien ajouté(s) ',Response::HTTP_OK); 

    }
}
