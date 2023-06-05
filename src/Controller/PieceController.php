<?php

namespace App\Controller;

use App\Entity\Montage;
use App\Entity\Piece;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class PieceController extends AbstractController
{
    #[Route('/showP', name: 'show_piece')]

    public function readPiece(Request $request){

        $em = $this->getDoctrine()->getManager();
        $repo = $em ->getRepository(Piece::class);
        $listpieces = $repo ->findAll();
        $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/pieces/';


        return $this ->render('piece/show.html.twig',array(
            'publicPath'=>$publicPath,
            'listpieces'=>$listpieces,

        ));
    }
    #[Route('/addPiece', name: 'add_piece')]
    public function ajouterPiece(Request $request){
        $publicPath="uploads/pieces/";
        $piece = new Piece();
        $form = $this ->createForm("App\Form\PieceType",$piece);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $image=$form->get('image')->getData();
            $em = $this ->getDoctrine()->getManager();
            if($image){
                $imageName=$piece->getNomPiece().'.'.$image->guessExtension();
                $image->move($publicPath,$imageName);
                $piece->setImage($imageName);
            }
            $em = $this ->getDoctrine()->getManager();
            $em ->persist($piece);
            $em->flush();
            $session = new Session();
            $session->getFlashBag()->add('notice', 'Piéce ajoutée avec succés');
            return $this->redirectToRoute('show_piece');
        }
        return $this->render('piece/add.html.twig',
            ['fP'=>$form->createView()]);
    }

}
