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
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Depot;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PartenaireController extends AbstractController


{
    private $userRepo;
    private $serializer;
    private $security;
    private $validator;
    private $compte;

    public function __construct(CompteRepository $compte,   ValidatorInterface $validator,EntityManagerInterface $manager   ,PartenaireRepository $part ,SerializerInterface $serializer ,Security $security ,UserRepository $userRepo)
    {
        $this->manager = $manager;
        $this->part=$part ;
        $this->serializer=$serializer;
        $this->security = $security;
        $this->userRepo = $userRepo;
        $this->validator = $validator;
        $this->compte=$compte;

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
       ->setSolde(700000)
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
                           $user->setCompte($partenaire->getCompte());


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


     //  a partir de la je vais creer la fonction editer un partenaire ,ou une agence 
     public function EditPartenaire(Request $request ,$id)
     {
         $json = json_decode($request->getContent());
         $partenaire=$this->part->find($id); 
      

         // verifions si le partenaire ou l agence existe deja 
         if($partenaire) {
           
          if($json) {
            foreach($json as $key=> $value) {
              $setProperty='set'.ucfirst($key);
              $partenaire->$setProperty($value);  
            
            }

            $erreurs = $this->validator->validate($partenaire);
            if ($erreurs) {
                return $this->json($erreurs);
            } 

            $this->manager->flush();
       return $this->json('Partenaire bien modifi\'é ',Response::HTTP_OK); 
            

          }
          else {
            return $this->json('mettez des données à modifier  ',Response::HTTP_OK); 
          }
          
                   
              ///dd($json->localisation);
             }
          else {
            return $this->json('ce partenaire n existe pas    ',Response::HTTP_OK);  
          }
        
        }



        // nous allos crrer la fonction qui bloque un partenaire et va  également bloquer tous les 
        // les utilisateurs de ce partenaire 
        public function BloquerPartenaire($id)
        {
    
            $partenaire=$this->part->find($id);
          if($partenaire) {
         
        
            $idcompte=$partenaire->getCompte()->getId();
            $compte=$this->compte->find($idcompte);
           //bloquons l admin agence  qui a creer cet agence ou le partenaire
              //    dd(count($compte->getUsers()));
              // for ($i=0; $i <count($compte->getUsers()) ; $i++) { 
              // if($compte->getUsers()[$i]->getProfil()->getLibelle()=="Admin-Partenaire") {
              //   dd($compte->getUsers()[$i]);
              // }
              // }
              
             
            $compte->setStatut(1);
             $partenaire->setStatut(1);
            //la nous allons bloquer les utilisateurs 
            $data=$partenaire->getUsers();
            foreach ( $data as $value) {
              $value->setStatut(1);
            }
            
            $this->manager->flush();
            return $this->json('ce partenaire et ses utilisateurs sont bloqués   ',Response::HTTP_OK);   
          }
          else {
            return $this->json('ce partenaire n existe pas    ',Response::HTTP_OK);  
          }

        }

        

        // On va creer une fonction pour faire un depot

          public function FaireDepot(Request $request  ) {


            $json = json_decode($request->getContent());
            
            // recuperer le compte dans le quel on souhaite faire le depot 
            $compte=$this->compte->findByNumeroCompte($json->ninea);
          
            if($compte) {
                 
                  $user=$this->security->getUser();
                  
                  $solde=$compte[0]->getSolde();
                  $montantDepot=$json->montant;
                  if($montantDepot<=0){
                    return $this->json('Le montant doit etre positif    ',Response::HTTP_OK);
                  }
                  $solde=$solde+$montantDepot;
                 
                  $depot= (new Depot)
                  ->setCaissier($user)
                  ->setDateDepot(new \DateTime())
                  ->setMontantDepot($montantDepot)
                  ;
                   $compte[0]->setSolde($solde);
                   $this->manager->persist($compte[0]);
                   $depot->setCompte($compte[0]);
                   $this->manager->persist($depot);
                   $this->manager->flush();
                   return $this->json('Nouveau depot ajouté    ',Response::HTTP_OK);
            }
            else {
              return $this->json('ce compte n existe pas    ',Response::HTTP_OK);
            }
  

          } 

          // on va creer la fonction qui permet d annuler un depot 
          public function AnnulerDepot( DepotRepository $depotRepo) {
           if ($this->isGranted('ROLE_Caissier')) {

         // je vais recuperer le dernier depot de l utilisateur 
         $dernierDepot=$depotRepo->findOneBy([],['id'=>'DESC']);
        $caissier=$this->getUser();
        if($caissier!=$dernierDepot->getCaissier()) {

          return $this->json('Impossible d annuler le depot   ',Response::HTTP_OK);
        } 
           // a ce stade on va verifier si l;a somme du dépot pourrait etre retirée dans e compte 
           // parce que il se pourrait qu une transaction soit faite  avant l annulation du depot
            $sommeDepot=$dernierDepot->getMontantDepot();
            $sommeCompte=$dernierDepot->getCompte()->getSolde();
           
         // vérifion si c possible d annuler le depot autrement si le montant a retirer est inférieur au solde du compte 
         if($sommeDepot>$sommeCompte) {
          return $this->json('Impossible d annuler le depot  car  une transactio  a été déja faite   ',Response::HTTP_OK);

         }

         // a partir de ce moment c possible d annuler le depot et de mettre a jour le compte

         $solde=$sommeCompte-$sommeDepot;
         $compte=$dernierDepot->getCompte();
          $compte->setSolde($solde);
         $compte->removeDepot($dernierDepot);

         $this->manager->persist($compte);
         $this->manager->remove($dernierDepot);
         $this->manager->flush();
         return $this->json('Dernier depot annulé     ',Response::HTTP_OK);
         
        } 
           else {
            return $this->json('vous n avez pas le droit d annuler un depot    ',Response::HTTP_OK);
           }
              
          } 
          


          
}
