<?php
namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     * @Method({ "GET" })
     */
    public function index()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig', array('articles' => $articles));
    }



    /**
     * @Route("/article/new", name="new_article")
     * Method({ "GET", "POST" })
     */
     public function new(Request $request)
     {
         $article = new Article();
         $form = $this->createFormBuilder($article)
             ->add('title', TextType::class, [
                     'attr' => [
                         'class' => 'form-control'
                     ]
                 ]
             )
            ->add('body', TextareaType::class, [
                    'required' => false,
                    'attr' => ['class' => 'form-control']
                ]

            )
            ->add('save', SubmitType::class, [
                'label' => 'Create',
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ])
            ->getForm();

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid())
         {
             $article = $form->getData();

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($article);
             $entityManager->flush();

             return $this->redirectToRoute('article_list');
         }

         //render the form
         return $this->render('articles/new.html.twig', [
             'form' => $form->createView()
         ]);
     }





    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({ "GET", "POST" })
     */
    public function edit(Request $request, $id)
    {
        $article = new Article();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        //pass that retrieved article into our form builder & it will know to auto populate the
        // form fields if the fields already have data in them from the DB or not
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('body', TextareaType::class, [
                    'required' => false,
                    'attr' => ['class' => 'form-control']
                ]

            )
            ->add('save', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            //since we are editing, we need not inform Doctrine whether we are persisting
            // or removing since it's not a new record we are adding. We just call flush()
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        //render the form
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/article/delete/{id}")
     * Method({ "DELETE" })
     */
     function delete(Request $request, $id)
     {
         $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

         $entityManager = $this->getDoctrine()->getManager();
         //this time we say we want to 'remove' not 'persist' (save) the data
         $entityManager->remove($article);
         $entityManager->flush();

         //the fetch API expects a response
         $response = new Response();
         $response->send();
     }





    /**
     * @Route("/article/save")
     */
//    public function save()
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $article = new Article();
//        $article->setTitle('Article 1');
//        $article->setBody('This is the Body for Article ');
//
//        //persist the article (tell it that u wanna save it)
//        $entityManager->persist($article);
//
//        //execute the query (by flushing)
//        $entityManager->flush();
//
//        return new Response('Saved an article with the id of '.$article->getId());
//    }
}