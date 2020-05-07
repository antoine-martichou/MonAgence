<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PropertyRepository;
use App\Entity\Property;
use App\Form\PropertyType;

class AdminPropertyController extends AbstractController
{

    private $repository;
    
    public function __construct(PropertyRepository $repository)
    {
        $this->repository = $repository;
       
    }

    /**
     * @Route("/admin", name="admin.property.index")
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig',[
            'properties' => $properties
        ]);
    }

    /**
     * @Route("/admin/property/create", name="admin.property.new")
     */
    public function newProperty(Request $request)
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->persist($property);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Bien créée avec succès ! ');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     */
    public function edit(Property $property, Request $request)
    {
        

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Bien modifié avec succès ! ');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
     */
    public function delete(Property $property, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token')))
        {
            $this->getDoctrine()->getManager()->remove($property);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Bien supprimé avec succès ! ');
        } 

        return $this->redirectToRoute('admin.property.index');
    }
}