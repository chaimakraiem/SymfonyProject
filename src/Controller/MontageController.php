<?php

namespace App\Controller;

use App\Entity\Montage;
use App\Entity\Piece;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MontageController extends AbstractController
{
    #[Route('/readMontage', name: 'read_montage')]
    public function readMontage(){
        $em = $this->getDoctrine()->getManager();
        $repo = $em ->getRepository(Montage::class);
        $lesmontage = $repo ->findAll();

        return $this ->render('montage\home.html.twig',['lesmontages'=>$lesmontage]);
    }
    #[Route('/addMontage', name: 'add_montage')]
    public function ajouterMontage(Request $request){
        $montage = new Montage();
        $form = $this ->createForm("App\Form\MontageType",$montage);
        $form->handleRequest($request);

            if ($form->isSubmitted()){
                $em = $this ->getDoctrine()->getManager();
                $em ->persist($montage);
                $em->flush();
                $session = new Session();
                $session->getFlashBag()->add('notice', 'Montage ajouté avec succés');
                return $this->redirectToRoute('read_montage');
            }
        return $this->render('montage/ajouter.html.twig',
            ['f'=>$form->createView()]);
        }

    #[Route('/editMontage/{id}', name:'update_montage')]

    public function updateAu(Request $request, $id) {

        $montage = $this->getDoctrine()->getRepository(Montage::class);
        $montage = $montage->find($id);

        if (!$montage) {
            throw $this->createNotFoundException(
                'There are no montages with the following id: ' . $id
            );
        }

        $form = $this ->createForm("App\Form\MontageType",$montage);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $montage = $form->getData();
            $em->flush();

            return $this->redirect($this->generateUrl('read_montage'));

        }

        return $this->render(
            'montage/edit.html.twig',
            array('f' => $form->createView())
        );

    }
    #[Route('/deleteMontage/{id}', name: 'delete_montage')]
    public function delete(Request $request, $id): Response
    {
        $c = $this->getDoctrine()
            ->getRepository(Montage::class)->find($id);
        if (!$c) {
            throw $this->createNotFoundException('No montage found for id ' . $id);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($c);

        $entityManager->flush();
        return $this->redirectToRoute('read_montage');
    }

    #[Route('/montage/{id}', name: '')]
    public function show($id,Request $request)
    {
        $montage = $this->getDoctrine()
            ->getRepository(Montage::class)
            ->find($id);
        $em = $this->getDoctrine()->getManager();
        $listpieces = $em
            ->getRepository(Piece::class)
            ->findBy(['Montage' =>$montage]);
        $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/pieces/';

        if (!$montage) {
            throw $this->createNotFoundException(
                'No montage found for id ' . $id);
        }
        return $this->render('montage/index.html.twig', array(
            'listpieces' => $listpieces,
            'montage' => $montage,
            'publicPath'=>$publicPath,

        ));
    }

}
