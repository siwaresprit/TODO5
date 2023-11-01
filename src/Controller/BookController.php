<?php

namespace App\Controller;
use App\Form\BookType;
use App\Entity\Book;
use App\Entity\Author;
use DateTime;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/book')]
class BookController extends AbstractController
{
    private EntityManagerInterface $entityManager;  // Inject the EntityManager

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
   
    #[Route('/add', name: 'addbook')]
    public function addProduct(ManagerRegistry $mr,Request $req, AuthorRepository $repauth): Response
    {
        $b=new Book();
        $form=$this->createForm(BookType::class,$b);
        $form->handleRequest($req);
                if ($form->isSubmitted() && $form->isValid()) { 
                    $em=$mr->getManager();
                    $authorBooks = $form->get('author')->getData();
                    $auth=$repauth->findOneById($authorBooks);
                    if ($authorBooks !== null) {
                    $res=$auth->getNbbooks(); 
                    $auth->setNbbooks($res+1); 
                    }
                    if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($b);
                    $em->flush();
                    }
                     $em->flush();
        }
      

        return $this->renderForm('book/add.html.twig',[
            'book' => $b,
            'f'=>$form,
        ]);

    }




    #[Route('/show', name: 'showlist')]
    public function Show(BookRepository $repo,ManagerRegistry $mr){  
    $books=$mr->getRepository(Book::class); 
    return $this->render('book/show.html.twig', [
        'b' => $books->findAll()
    ]);

    }


    #[Route('/edit/{id}', name: 'edit_book')]
    public function editBook(Request $request, int $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('showlist'); // Replace with your list route
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/delete/{id}', name: 'delete_book')]
    public function deleteBook($id, ManagerRegistry $mr,BookRepository $repo ){
        $em=$mr->getManager(); 
        $b=$repo->find($id);
        $em->remove($b);
        $em->flush();
        return new Response('removed');
       
    }
     

    #[Route('/book/{id}', name: 'showd_book')]
    public function showDetails(BookRepository $bookRepository, int $id): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        return $this->render('book/show_book.html.twig', [
            'book' => $book,
        ]);
    }




    #[Route("/showbyau", name: 'showbyau')]

    public function searchBookBytitle(Request $request, BookRepository $rep): Response {
        $searchTerm = $request->query->get('search');
        $result = $rep->findBySearchTerm($searchTerm);
    
        return $this->render('book/show.html.twig', [
            'search' => $searchTerm, // Pass the search term as a variable
            'b' => $result,
        ]);
    }

   


    #[Route('/published2023', name: 'published')]
    public function booksPublishedBefore2023AndAuthorMoreThan10(BookRepository $bookRepository): Response
    {
        $filteredBooks = $bookRepository->findByTime();

        return $this->render('book/published.html.twig', [
            'books' => $filteredBooks,
        ]);
    }


    #[Route('/cat', name: 'cat')]
    public function listBooks(BookRepository $bookRepository): Response
    {
        $bookRepository->updateBooksCategory(); 
        $books = $bookRepository->findAll();

        return $this->render('book/show.html.twig', [
            'b' => $books,
        ]);
    }


    #[Route('/countromance', name: 'countromance')]
    public function countRomanceBooks(BookRepository $bookRepository): Response
    {
        $count = $bookRepository->countBooksInRomanceCategory();

        return $this->render('book/count.html.twig', [
            'count' => $count,
            
        ]);
    }



    #[Route('/betweendates', name: 'betweendates')]
    public function booksPublishedBetweenDates(EntityManagerInterface $em): Response
    {
        
        $startDate = new DateTime('2014-01-01');
        $endDate = new DateTime('2018-12-31');

        $dql = "SELECT b FROM App\Entity\Book b
                WHERE b.publicationDate BETWEEN :startDate AND :endDate";
        $query = $em->createQuery($dql)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $books = $query->getResult();

        return $this->render('book/betweendates.html.twig', [
            'books' => $books,
        ]);
    }




}
