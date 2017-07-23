<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Shortener;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $current_url = new Shortener();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($current_url)
            ->add('url', TextType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {

            $data = $form->getData();
//            dump($data);
//            dump($data->getUrl());
//            die();

            $exist_url = $this->getDoctrine()->getRepository('AppBundle:Shortener')->findOneBy([
               'url' => $data->getUrl()
            ]);
            if ($exist_url){
                $current_url->setShortUrl(base64_encode($exist_url->getId()).'.com');
            } else {
                $em->persist($current_url);
                $em->flush();

                $current_url->setShortUrl(base64_encode($current_url->getId()).'.com');

                $em->flush();
            }



            return $this->render('AppBundle:main:index.html.twig',[
                'form' => $form->createView(),
                'newUrl' => $current_url
            ]);
        }

        // replace this example code with whatever you need
        return $this->render('AppBundle:main:index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}