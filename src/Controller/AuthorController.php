<?php

namespace App\Controller;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

#[Route('/author')]
class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

  



    #[Route('/show', name: 'showlistauthor')]
    public function listAuthorByEmail(AuthorRepository $repo){
    return $this->render('author/show.html.twig', [
    'a' => $repo->findAll()
    ]);
    
}
    
    
    
    #[Route('/delete0/{id}', name: 'delete0_author')]
    public function deleteAuthorsWithZeroBooks(int $id, EntityManagerInterface $em, AuthorRepository $repo)
    {
        $author = $repo->find($id);
    
        if ($author !== null) {
            $em->remove($author);
            $em->flush();
    
            return new Response('Author with ID ' . $id . ' removed.');
        } else {
            return new Response('Author with ID ' . $id . ' not found.');
        }
    }
    


    
    #[Route('/search', name: 'search')]
    public function searchAuthorsByBookCountDQL(Request $request, EntityManagerInterface $em, AuthorRepository $authorRepository): Response
    {
        $minBooks = $request->get('min_books');
        $maxBooks = $request->get('max_books');

        if (!empty($minBooks) && !empty($maxBooks)) {
            $authors = $authorRepository->findAuthorsByBookCountRangeDQL($minBooks, $maxBooks);
        } else {
            // Handle the case where the search parameters are not provided
            // You can display a message or return an empty result
            $authors = [];
        }

        return $this->render('author/search.html.twig', [
            'authors' => $authors,
            'minBooks' => $minBooks,
            'maxBooks' => $maxBooks,
        ]);
    }


    #[Route('/deleteauthor/{id}', name: 'deleteauthor')]
    public function deleteAuthors(int $id, EntityManagerInterface $em, AuthorRepository $repo)
    {
        $author = $repo->find($id);
    
        if (!$author) {
            return new Response('Author not found');
        }
    
        $authorsZero = $repo->findAuthorsZero();
    
        if (in_array($author, $authorsZero)) {
            $em->remove($author);
            $em->flush();
    
            return new Response('Author deleted');
        } else {
            return new Response('Author has associated books and was not deleted');
        }
    }

}







