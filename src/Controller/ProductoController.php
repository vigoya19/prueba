<?php

namespace App\Controller;



use App\Entity\Producto;
use App\Entity\Categoria;
use App\Form\PorductoType;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\validate;


/**
 * @Route("/producto")
 */
class ProductoController extends Controller
{
    /**
     * @Route("/", name="producto_index", methods={"GET"})
     */
    public function index(productoRepository $productoRepository): Response
    {

        $em = $this->getDoctrine()->getManager();

        $sql = 'SELECT p.id, p.nombre, p.categoria, p.codigo, c.nombre AS nombreC, p.descripcion, p.marca, p.precio FROM producto p INNER JOIN categoria c
         ON c.id = p.categoria';
        
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();

         $result = $statement->fetchAll();
          $data = $this->get('jms_serializer')->serialize($result, 'json');
          return new JsonResponse(json_decode($data), Response::HTTP_ACCEPTED);

}
    /**
     * @Route("/new", name="producto_new", methods={"GET","POST"})
     */
    public function new(Request $request, validate $validate): Response
    { 

        $producto = new Producto();
        
        $data = $request->getContent();//Obtengo mi data
        $data = $this->get('jms_serializer')->deserialize($data,'App\Entity\Producto', 'json');
        $em = $this->getDoctrine()->getManager();
        //  $categoria = $this->getDoctrine()->getRepository('App:Categoria')->find($id); 
         $producto->setCodigo($data->getCodigo());
         $producto->setNombre($data->getNombre());
         $producto->setDescripcion($data->getDescripcion());
         $producto->setMarca($data->getMarca());
         $producto->setPrecio($data->getPrecio());
         $producto->setCategoria($data->getCategoria());
         $em->persist($producto);
        //  $em->persist($categoria);
         $em->flush();

        return new JsonResponse(['msg'=>'Bien','staus'=>'succes','data'=>Response::HTTP_ACCEPTED]);

       }

         /**
         * @Route("/{id}", name="producto_show", methods={"GET"})
        */
    public function show($id): Response
    {
        $em = $this->getDoctrine()->getManager();

       $sql = 'SELECT c.id,c.codigo, c.nombre, c.descripcion,c.activo FROM  categoria c  INNER JOIN producto p ON p.categoria = c.id AND c.id ='.$id;
        
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();

         $result = $statement->fetchAll();
          $data = $this->get('jms_serializer')->serialize($result, 'json');
          return new JsonResponse(json_decode($data), Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/{id}/edit", name="producto_edit", methods={"GET","PUT"})
     */
    public function edit($id,Request $request): Response
    {
   
        $data = $request->getContent();//Obtengo mi data
        $data = $this->get('jms_serializer')->deserialize($data,'App\Entity\Producto', 'json'); //Deserializo mi data para que php lo pueda entender como array
        $em = $this->getDoctrine()->getManager(); 
        $producto = $em->getRepository(Producto::class)->find($id); //Busco mi producto 

        if(isset($producto)){ //Si mi producto trae resultados
            $producto->setCodigo($data->getCodigo());
            $producto->setNombre($data->getNombre());//le seteo sus respectivos campos
            $producto->setDescripcion($data->getDescripcion());
            $producto->setMarca($data->getMarca());
            $producto->setPrecio($data->getPrecio());
            $producto->setCategoria($data->getCategoria());
            $em->flush();
        }
        
        $updateproducto = $this->get('jms_serializer')->serialize($producto, 'json'); // de lo contrario retorno mi array de objetos
        // return new JsonResponse(array('nombre'=>$data->getNombre(),'productoArray'=>$producto), Response::HTTP_ACCEPTED);
           return new JsonResponse(['msg'=>'edicion exitosa','status'=>'succes','data'=>$data], Response::HTTP_ACCEPTED);// return new JsonResponse($data->nombre);
    }
     /**
     * @Route("/{id}", name="producto_delete", methods={"DELETE"})
     */
    // ue es request? La clase Request representa la petición HTTP 
    // siguiendo la filosofía de orientación a objetos. Con ella, tienes toda 
    // la información a tu alcance:
    public function delete($id): Response 
    {
         $em = $this->getDoctrine()->getManager(); 
         $producto = $em->getRepository(producto::class)->find($id); //Busco mi producto 
           
    if(isset($producto)){
        $em->remove($producto);
        $em->flush();
             return new JsonResponse(['msg'=>'Eliminado','status'=>'succes','producto Eliminado' => $this->json($producto)], Response::HTTP_ACCEPTED);
                }else{
            return new JsonResponse(array('msg'=>'Error','producto No Eliminado' => $this->json($producto) ), Response::HTTP_NOT_FOUND);
                    
         }
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
       }



}
