<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface; //uso las validaciones nativas de symfony
use Doctrine\Bundle\DoctrineBundle\Registry;

class validate {


private $validator;
private $em;

public function __construct(ValidatorInterface $validator, Registry $registry){
     $this->validator = $validator;
     $this->em = $registry;
}

public function validateRequest($data){
    $errors = $this->validator->validate($data);
    $errorResponse = array();
  
  foreach ($errors as $error){
   $errorResponse[] = [
       'field' => $error->getPropertyPath(),
       'message' => $error->getMessage()
   ];
}

if(count($errors)>0){
    return ['errors' => $errorResponse];
}else{
    return [];
}


}



}


