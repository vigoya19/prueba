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
//     public function getArticles(Request $request): Response
// {
//     $em = $this->getDoctrine()->getManager();
//     $categoria = $em->getRepository(Categoria::class)->findAll();

//     return new Response($this->json($categoria), Response::HTTP_OK);
// }
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
    public function new(Request $request): Response
    { 
         $data = $request->getContent();
         $categoria = $this->get('jms_serializer')->
         deserialize($data,'App\Entity\Categoria', 'json');
    //    $errors = $validate->validateRequest($categoria);
       
    //    if(!empty($errors)){
    //        return new JsonResponse(
    //            ['msg'=>'Error al Guardar',
    //              'error'=>$errors],
    //              Response::HTTP_BAD_REQUEST);
           
    //    }
         var_dump($categoria);
         exit();
        // $categorium = new Categoria();
        // $form = $this->createForm(CategoriaType::class, $categorium);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->persist($categorium);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('categoria_index');
        // }

        // return $this->render('categoria/new.html.twig', [
        //     'categorium' => $categorium,
        //     'form' => $form->createView(),
        // ]);
    }

    /**
     * @Route("/{id}", name="categoria_show", methods={"GET"})
     */
    public function show(Categoria $categorium): Response
    {
        return $this->render('categoria/show.html.twig', [
            'categorium' => $categorium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categoria_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categoria $categorium): Response
    {
        $form = $this->createForm(CategoriaType::class, $categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categoria_index', [
                'id' => $categorium->getId(),
            ]);
        }

        return $this->render('categoria/edit.html.twig', [
            'categorium' => $categorium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categoria_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Categoria $categorium): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorium->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categoria_index');
    }
}
