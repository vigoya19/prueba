<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface; //uso las validaciones nativas de symfony
use Symfony\Bridge\Doctrine\RegistryInterface;

class validate {


private $validator;
private $em;

public function __construct(ValidatorInterface $validator, RegistryInterface  $registry){
     $this->validator = $validator;
     $this->em = $registry;
}

public function validateRequest($data){
    $errors = $this->validator->validate($data);
    $errorResponse = [];
    
  foreach ($errors as $error){
   $errorResponse[] = [
       'field' => $error->getPropertyPath(),
       'message' => $error->getMessage()
   ];
}

if(count($errors) > 0){
    return $errorResponse;
}else{
    return [];
}


}

// public function validateJson(Request $request){
//     $data = $request->getContent();

//     if(!$this->isJson($data)){
//      return new JsonResponse(['msg'=> 'Bad Request'], Response::HTTP_BAD_REQUEST);

//     }
// }



}


