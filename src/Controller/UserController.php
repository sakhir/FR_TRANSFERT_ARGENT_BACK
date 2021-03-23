<?php

namespace App\Controller;


use App\Entity\Profil;
use App\Helper\UserHelper;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UserController extends AbstractController


{

    private $security;
    private $em ;
    private $helper;
    private $repo ;
    private $profilRepo ;
    private $encode;  


    public function __construct(Security $security ,EntityManagerInterface $em ,UserHelper $helper ,UserRepository $repo ,ProfilRepository $profilRepo 
   , UserPasswordEncoderInterface $encode
    )
    {
        $this->security = $security;
        $this->em=$em ;
        $this->helper=$helper ;
        $this->repo=$repo ;
        $this->profilRepo=$profilRepo ;
        $this->encode = $encode;
    }
        // creeons une fonction addUser qui va creer tout type d utilisateur 
    
        public function addUser(UserHelper $helperUser,Request $request  ) {
            $userpost =$request->request->all();
            if(isset($userpost['avatar'])){
                unset($userpost['avatar']);
                
            }
            
           $helperUser->createUser($request);
           return $this->json('User added successfully',Response::HTTP_OK);
       
       }

       // on crrer la fonction qui permet de modifier un utilisateur 
       public function EditUser($id,Request $request,SerializerInterface $serializer) {
        $data=$request->request->all();
        if(isset($data['avatar'])){
            unset($data['avatar']);
            
        }
        
        $user=$this->repo->find($id);
        
        //
        foreach($data as $key=> $value) {
            $setProperty='set'.ucfirst($key);
            
            if($key!='profil') {
                    if($key=='agence') {
                        $value=$this->part->find($data['agence']);
                    }
                    if($key=='password') {
                     $value=$this->encode->encodePassword($user,$data['password']);
                    }
                    
                  $user->$setProperty($value);  
             
            }
            else  {
                $profil=$this->profilRepo->find($data['profil']); 
               
                $user->setProfil($profil);
            }
              
     }

           
       
        $image=$this->helper->TRaiterImage($request);
        $user->setAvatar($image);
        $this->em->flush();
         return $this->json('Modification reuissie',Response::HTTP_OK);
        //
       
    }


    //Cette fonction cherche a bloquer un utilisateur d une agence de facto il pourra pas faire des transactoins  
    public function deleteUser(UserRepository $repo,$id,EntityManagerInterface $em)
    {
        $user = $repo->find($id);
  
        $CurrentUser=$this->getUser();
         
            if ($this->isGranted('ROLE_Super-Admin') && $user != null) {
                $user->setIsdeleted(1);
                $em->flush();
                return $this->json('Utilisateur Supprimé avec succes  ',Response::HTTP_OK); 
            }
            return $this->json("Vous n avez pas le droit de faire cet opération ou  utilisateur inexistant !!!");
         

       
    }

     public function BloquerUser(UserRepository $repo,$id,EntityManagerInterface $em)
    {
        $user = $repo->find($id);


           
            if ($this->isGranted('ROLE_Super-Admin') && $user != null) {
                $user->setStatut(1);
                $em->flush();
                return $this->json('Utilisateur bloqué avec succes  ',Response::HTTP_OK); 
            }
            return $this->json("Vous n avez pas le droit de faire cet opération ou  utilisateur inexistant !!!");
         

       
    }  

 }
