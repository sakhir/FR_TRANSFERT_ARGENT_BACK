<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\UserRepository;
use App\Repository\CompteRepository;
use App\Repository\CommissionRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
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


    public function TrouverTarif(Request $request, CommissionRepository $commissionRepository) {
        $data=json_decode($request->getContent(),true);
        //dd($data['montant']);
        $montant=0;
        $i=0;
        if (!is_numeric($data['montant']) || $data['montant']<0 ) {
            return $this->json( 'Impossible de donner le tarif pour ce montant ',Response::HTTP_OK);
           }
    
        $tester=$commissionRepository->findAll();
       while ($i<count($tester)) {
           if ($data['montant']<=$tester[$i]->getBorneSuperieur() && $data['montant']>=$tester[$i]->getBorneInferieur()) {
               $montant = $tester[$i]->getValeur();
               return $this->json($montant);
               break;
           }
           $i++;
       }

    }

    public function EnvoyerArgent(Request $request, CommissionRepository $commissionRepository, CompteRepository $compteRepository)
    {
       
        $data = $request->request->all();
        
        $montant = 'montantEnvoyer';
        $montants = 'montantEnvoyer';
        $i=0;
        $transaction = new Transaction();
        $errors = [];
        $tester=$commissionRepository->findAll();
        if($data[$montant]<=0) {
            $errors[] = "Choisissez une valeure strictement  positive ";
            return $this->json($errors);
    
           }
       // dd($tester);
       $tester=$commissionRepository->findAll();
       while ($i<count($tester)) {
           if ($data[$montant]<=$tester[$i]->getBorneSuperieur() && $data[$montant]>=$tester[$i]->getBorneInferieur()) {
               $montant = $tester[$i]->getValeur();
               break;
           }
           $i++;
       }
       if (!is_numeric($montant)) {
           $errors[] = "On ne peut pas faire une transaction pour cette  sommee";
       }
      
       // on va mettre a jour les infos que l utilisateur a entré
       foreach($data as $key=> $value) {
           
        $setProperty='set'.ucfirst($key);
        $transaction->$setProperty($value);  
      
      }
      // a partir de la nous allons faire la mise a jour des autres données dans la transaction
      $transaction->setCommissionTTC($tester[$i]);
      $transaction->setType(false);
      $transaction->setUserDepot($this->security->getUser());
      $transaction->setTotalEnvoyer($data[$montants]+ $montant);
      $codeTrans=substr($data['cinEnvoyeur'],8,4).substr($data['telEnvoyeur'],0,6).''.rand(1000,9999);
      $transaction->setCodeTransaction($codeTrans);
      $transaction->setDateEnvoie(new \DateTime());
      $transaction->setCommissionEtat(($montant*30)/100);
      $transaction->setCommissionSystem(($montant*40)/100);
      $transaction->setCommissionEnvoi(($montant*10)/100);
      $comptepartenaire = $this->getUser()->getCompte();

      if ($comptepartenaire == NULL || $comptepartenaire->getPartenaire()!=$this->getUser()->getPartenaire() || $comptepartenaire->getSolde()<= $data[$montants]+ $montant) {
        $errors[]='Vous ne pouvez pas faire de transaction car on ne vous a pas assigné de compte ou Vous n êtes pas habilités à faire cet operation   ou solde insuffisant';
    }
   
    //  vérifions si l utilisateur qui souhaite faire la transaction est bloqué ou non 
    if ($this->getUser()->getIsdeleted()=="1") {
        return $this->json('Vous ne pouvez pas faire de transaction car  vous etes  bloqué ',Response::HTTP_OK);       
    }


    // verifions a pôssibilité comme quoi le compte dans lequel on souhaite faire les transactions soit bloqué
   
   
    if ($this->getUser()->getCompte()->getStatut()!=null || $this->getUser()->getCompte()->getStatut()=="1" ) {
        return $this->json('Vous ne pouvez pas faire de transaction car  votre compte est bloqué ',Response::HTTP_OK);       
    }

    

    if ($errors) {
        return $this->json($errors);
    }
    
    $comptepartenaire->setSolde($comptepartenaire->getSolde() - ($data[$montants]+ $montant) + (($montant*10)/100));
    $this->manager->persist($transaction);
    $this->manager->flush();

    return $this->json('Transaction avec le numéro : '.$transaction->getCodeTransaction().'  bien éffectué  ' ,Response::HTTP_OK);
    
    return $this->json('Une erreur s est produite , réessayer  à nouveau  ',Response::HTTP_OK);

         
    }


    public function RetirerArgent(Request $request, CompteRepository $compteRepository, Transaction $transaction=null, TransactionRepository $transactionRepository)
    {
        $values = $request->request->all();
        $transaction = $transactionRepository->findByCodeTransaction($values['codeTransaction']);
       
        if ($transaction==NULL) {
            $errors[] = 'cet transaction n\'existe pas dans la base' ;
        }

       
       
        if ($transaction[0]->getType()==true) {
            return $this->json('La transaction a été déja retirée  ',Response::HTTP_ACCEPTED);
        }


       // l envoyeur c est l utilisateur de l agence qui  qui a envoyé l argent 
        $envoyeur=$transaction[0]->getUserDepot();
        $errors = [];

        // vérifions si le compte de utilisateur  est bloqué  avant de retirer 
        if ($this->getUser()->getCompte()->getStatut()!=null || $this->getUser()->getCompte()->getStatut()=="1" ) {
            return $this->json('Vous ne pouvez pas faire de transaction car  le compte est bloqué ',Response::HTTP_OK);       
        }

        //  vérifions si l utilisateur qui souhaite faire la transaction est bloqué ou non
       
    if ($this->getUser()->getIsdeleted()=="1") {
        return $this->json('Vous ne pouvez pas faire de transaction car  vous etes  bloqué ',Response::HTTP_OK);       
    }
       
        // on va commencer par la inserer les informations donc la mise a jour 
        $transaction[0]->setUserDepot($envoyeur);
        $transaction[0]->setCinBeneficiaire($values['cinBeneficiaire']);
        $transaction[0]->setUserRet($this->getUser());
        $transaction[0]->setTotalEnvoyer($transaction[0]->getTotalEnvoyer());
        $transaction[0]->setDateRetrait(new \DateTime());
        $transaction[0]->setType(true);
        $transaction[0]->setMontantRetirer($transaction[0]->getTotalEnvoyer() - $transaction[0]->getCommissionTTC()->getValeur());
        $transaction[0]->setCommissionRetrait(($transaction[0]->getCommissionTTC()->getValeur()*20)/100);
        $comptepartenaire = $this->getUser()->getCompte();
        //recuperons le compte de celui qui a fait le depot
        $comptD=$transaction[0]->getUserDepot(); 
        //dd($comptD->getPartenaire()->getId());
       // dd($this->getUser()->getPartenaire()->getId());
        if ($comptepartenaire == NULL || $comptD->getPartenaire()->getId()!=$this->getUser()->getPartenaire()->getId()) {
        
            $errors[]='Vous ne pouvez pas faire de transaction car vous n avez pas le droit d utiliser ce compte ou vous etes un voleur ';
            $this->json($errors,Response::HTTP_OK);
        }       
        
      
        if (!$errors) { 
            $comptepartenaire->setSolde($comptepartenaire->getSolde() + ($transaction[0]->getTotalEnvoyer() - $transaction[0]->getCommissionTTC()->getValeur()) + ($transaction[0]->getCommissionTTC()->getValeur()*20)/100);
        
            $this->manager->persist($transaction[0]);
        $this->manager->flush();
        return $this->json('Le retrait fait avec succes : '.$transaction[0]->getCodeTransaction().' avec le montant  '.$transaction[0]->getMontantRetirer() ,Response::HTTP_OK);


        }

        else {
            return $this->json($errors);
        }



         
    }

    // trouver une transaction a partir du code  
    public function TrouverCoder(Request $request,  Transaction $transaction=null, TransactionRepository $transactionRepository, SerializerInterface $serializer)
    {
        $values = $request->request->all();
        $transaction = $transactionRepository->findByCodeTransaction($values['codeTransaction']);
       
        if ($transaction==NULL) {
            return $this->json('code in\'existant',Response::HTTP_ACCEPTED);
        }
        else {
            $data = $serializer->serialize($transaction[0], 'json', [ 'groups' => 'transaction']);
            return new Response(
                $data,200,[
                    'Content-Type' => 'application/json'
                ]
             );

                
        }

    }
      
    
       // on va creer une fonction qui va recuperer les transactions d un utilisateur 

             //
             public function AffciherTransactionUser(Request $request,$id, TransactionRepository $TranRepo, UserRepository $userRepo  ) {
                
                $userd=$TranRepo->findByUserDepot($id);
                $userR=$TranRepo->findByUserRet($id);
                
                

                if($userd) {
                   
                   // $userTransactionr=$user->getTransactionsr();
                
                   return $this->json($userd,Response::HTTP_OK,[],['groups'=>"transaction"]);
                }
                   
                if($userR)  {
                        return $this->json($userR,Response::HTTP_OK,[],['groups'=>"transaction"]);    
                    } 
               
                else {
                    return $this->json('Lutilisateur iniexistant  ou na pas encore fait de transaction',Response::HTTP_ACCEPTED);  
                }

            }

          
            // fonction qui liste les transactions d un partenaire 

            //AffciherTransactionsAgence
            public function AfficherTransactionsAgence(Request $request,$id, TransactionRepository $TranRepo, UserRepository $userRepo  ) {
                 
                    $agence=$this->part->find($id);
                    if($agence) {
                        dd('ok');

                    
                    }
                    else {
                        return $this->json('L agence n existe pas ',Response::HTTP_ACCEPTED);

                    }
       
            }


        // je vais creer la fonction qui annulle  une transaction

        public function AnnulerTransaction(Request $request, CompteRepository $compteRepository, Transaction $transaction=null, TransactionRepository $transactionRepository ) {
         $values = $request->request->all();
         
        $transaction = $transactionRepository->findByCodeTransaction($values['codeTransaction']);
       
        if ($transaction==NULL) {
            $errors[] = 'cet transaction n\'existe pas dans la base' ;
            return $this->json($errors);
        }
        if ($transaction[0]->getType()==true) {
            return $this->json('La transaction a été déja retirée  ',Response::HTTP_ACCEPTED);
        }
        // vérifions si le compte de utilisateur  est bloqué  avant d annuler 
        if ($this->getUser()->getCompte()->getStatut()!=null || $this->getUser()->getCompte()->getStatut()=="1" ) {
            return $this->json('Vous ne pouvez annuler  car  votre  compte est bloqué ',Response::HTTP_OK);       
        }
        
       // mon compte doit diminuer d;onc je dois faire  une soustraction sur mon compte 

            $totalAretirer=$transaction[0]->getMontantEnvoyer()+($transaction[0]->getCommissionTTC()->getValeur()*20)/100 +
            $transaction[0]->getCommissionEtat()+($transaction[0]->getCommissionSystem())/2; 
           
            // je vais  a present recuperer le compte de l utilisateur connecté et enlever ce montant 
            $comptepartenaire = $this->getUser()->getCompte();
            $comptepartenaire->setSolde($comptepartenaire->getSolde()- $totalAretirer);
            $this->manager->persist($comptepartenaire);
            $transaction[0]->setCommissionEtat(0);
            $transaction[0]->setCommissionSystem($transaction[0]->getCommissionSystem()/2);
        
           $this->manager->persist($transaction[0]);
           $this->manager->flush();
           return $this->json('Depot annulé avec succes : ',Response::HTTP_OK);

           
              

        }
        


 

}
