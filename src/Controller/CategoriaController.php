<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\validate;

/**
 * @Route("/categoria")
 */
class CategoriaController extends Controller
{
    /**
     * @Route("/", name="categoria_index", methods={"GET"})
     */
    public function index(CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll(); // consulto todas las categorias
        if(!isset($categorias)){
            return new JsonResponse(['mgs'=>'get no found'], Response::HTTP_NOT_FOUND); //si no hay ninguna categoria reyotno un json de error
        }else{
   
       $data = $this->get('jms_serializer')->serialize($categorias, 'json'); // de lo contrario retorno mi array de objetos
       return new JsonResponse(json_decode($data), Response::HTTP_ACCEPTED);
        }
       }

    /**
     * @Route("/new", name="categoria_new", methods={"GET","POST"})
     */
    public function new(Request $request, validate $validate): Response
    { 
         $data = $request->getContent();
      
         if(!$this->isJson($data)){
             return new JsonResponse(['msg'=> 'No es un Json valido'], Response::HTTP_BAD_REQUEST);
            }
      
         $categoria = $this->get('jms_serializer')->deserialize($data,'App\Entity\Categoria', 'json');
         $errors = $validate->validateRequest($categoria);
       
        if(!empty($errors)){
            return new JsonResponse(
                ['msg'=>'Error al Guardar',
                  'error'=>$errors],
                 Response::HTTP_BAD_REQUEST);
 
        }
             $em = $this->getDoctrine()->getManager();
             $em->persist($categoria);
             $em->flush();
             var_dump($categoria);
             exit();
       }

    public function show($id): Response
    {

        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository(Categoria::class)->find($id);
            
        $data = $this->get('jms_serializer')->serialize($categoria, 'json'); // de lo contrario retorno mi array de objetos
        return new JsonResponse(json_decode($data), Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/{id}/edit", name="categoria_edit", methods={"GET","PUT"})
     */
    public function edit($id,Request $request): Response
    {
        //  $cat = new Categoria;
        $data = $request->getContent();//Obtengo mi data
        $data = $this->get('jms_serializer')->deserialize($data,'App\Entity\Categoria', 'json'); //Deserializo mi data para que php lo pueda entender como array
        $em = $this->getDoctrine()->getManager(); 
        $categoria = $em->getRepository(Categoria::class)->find($id); //Busco mi categoria 

        if(isset($categoria)){ //Si mi categoria trae resultados
            
            $categoria->setNombre($data->getNombre());//le seteo sus respectivos campos
            $categoria->setCodigo($data->getCodigo());
            $categoria->setDescripcion($data->getDescripcion());
            $categoria->setActivo($data->getActivo());
             $em->flush();
        }
        
        $updateCategoria = $this->get('jms_serializer')->serialize($categoria, 'json'); // de lo contrario retorno mi array de objetos
        // return new JsonResponse(array('nombre'=>$data->getNombre(),'categoriaArray'=>$categoria), Response::HTTP_ACCEPTED);
           return new JsonResponse(json_decode($updateCategoria), Response::HTTP_ACCEPTED);// return new JsonResponse($data->nombre);
    }
     /**
     * @Route("/{id}", name="categoria_delete", methods={"DELETE"})
     */
    // ue es request? La clase Request representa la petición HTTP 
    // siguiendo la filosofía de orientación a objetos. Con ella, tienes toda 
    // la información a tu alcance:
    public function delete($id, Request $request): Response 
    {
         $em = $this->getDoctrine()->getManager(); 
         $categoria = $em->getRepository(Categoria::class)->find($id); //Busco mi categoria 
           
    if(isset($categoria)){
        $em->remove($categoria);
        $em->flush();
             return new JsonResponse(array('msg'=>'Eliminado','Categoria Eliminada' => $this->json($categoria) ), Response::HTTP_ACCEPTED);
                }else{
            return new JsonResponse(array('msg'=>'Error','Categoria No Eliminada' => $this->json($categoria) ), Response::HTTP_ACCEPTED);
                    
         }
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
       }




}
