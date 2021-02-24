<?php

namespace App\Helper;

use App\Entity\User;
use App\Entity\Profil;
use App\Repository\ProfilRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHelper   

{    private $manager;
     private $encode;  
     private $request;
     private $profilRepo ;
     private $part;
     private $serializer;

    public function __construct(EntityManagerInterface $manager , UserPasswordEncoderInterface $encode ,ProfilRepository $profilRepo ,PartenaireRepository $part ,SerializerInterface $serializer)
    {
        $this->manager = $manager;
        $this->encode = $encode;
        $this->profilRepo=$profilRepo ;
        $this->part=$part ;
        $this->serializer=$serializer;
    }

    public function createUser($request) {
      

   $data=$request->request->all();
   $profil=$this->profilRepo->findById($data['profil']);
  if(isset($data['agence'])) {
    $agence=$this->part->find($data['agence']);
  }
   
    unset($data['agence']);
    unset($data['profil']);
    //dd($profil);
    $user=$this->serializer->denormalize($data, User::class);
    $user->setProfil($profil[0]);
    $user->setIsdeleted(0);
    $user->setStatut(0);
    
    if(isset($agence)) {
        $user->setPartenaire($agence);
    }
    else {
        $user->setPartenaire(null);  
    }
    
    $user->setPassword($this->encode->encodePassword($user,$data['password']));

    $image=$this->TraiterImage($request);
     $user->setAvatar($image);
     
        $this->manager->persist($user);
         $this->manager->flush();
    return $user;

    }
   
    public function TRaiterImage($request) 
    {
        $avatar = $request->files->get("avatar");
       
        if($avatar) {
             $image = fopen($avatar->getRealPath(),"rb");
             return $image;     
            }
        else 
             {
                return null;
             }
        
       

    } 

}