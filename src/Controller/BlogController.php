<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface as ORMEntityManagerInterface;
use Doctrine\Persistence\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;




class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
            
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("blog/new", name="blog_create")
     * @Route("blog/{id}/edit", name="blog_edit")
     */

    //Création formulaire
    public function form(Article $article = null, Request $request, ORMEntityManagerInterface $manager) {

        if(!$article) {
            $article = new Article();
        }
         
        
        $form = $this->createFormBuilder($article)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formCrud' => $form->createView(),
            'editMode' => $article->getId() !== null,
            'article' => $article
        ]);
    }   

        //Supprimer un article

        /**
         * @Route("blog/{id}/remove", name="blog_remove")
         */

        public function remove (Article $article): Response
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            return $this->redirectToRoute('home');

        }



    /**
     * @Route("/blog/{id}", name="blog_show")
     */

    public function show(Article $article):Response
    {

        return $this->render('blog/show.html.twig', [
                'article' => $article
        ]);
    }

}

