<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Property;
use App\Form\ContactType;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use App\Notification\ContactNotification;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PropertyController extends AbstractController
{   
    private $repository;

    public function __construct(PropertyRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * @Route("/property", name="property.index")
     * 
     */
    public function index(PaginatorInterface $paginator, Request $request )
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);
        


        $properties = $paginator->paginate(
        $this->repository->findAllVisible($search),
        $request->query->getInt('page', 1),
        8//Nombre limit par page 
        );
        return $this->render('property/index.html.twig', [
           'properties' => $properties, 
           'form'       => $form->createView()
        ]);
    }
// 
    /**
     * @Route("/property/{id}", name="property.show")
     */
    public function show( $id, Request $request, ContactNotification $notification)
    {
        $repo = $this->getDoctrine()->getRepository(Property::class);
        $property =  $repo->find($id);

        $contact = new Contact();
        $contact->setProperty($id);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if( $form->isSubmitted() &&  $form->isValid() )
        {
            $notification->notify($contact);
            $this->addFlash('success','Votre message a bien été envoyé !');
            $this->redirectToRoute('property.show', [
                'id' => $property->getId()
            ]);
        }


        return $this->render('property/show.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);

    }
}
