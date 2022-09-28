<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\News;

class NewsController extends AbstractController
{
    public $doctrine;
 

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/news', name: 'app_news')]
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {

        $sql = "SELECT bp FROM App\Entity\News bp ORDER BY bp.id DESC";
        $query = $doctrine->getManager()->createQuery($sql);

        $result = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $request->query->getInt('limit', 10)/*limit per page*/
        );
    
        return $this->render('news/index.html.twig', [
            'controller_name' => 'NewsController',
            'news' => $result,
        ]);
    }


    public function save($data): Response
    {
        
        $entityManager = $this->doctrine->getManager();
    
        
        $news = $entityManager->getRepository(News::class)->findOneBy(['Title' => $data['title']]);
        if($news) {
            $news->setUpdatedAt(new \DateTimeImmutable());
            $news->setDescription($data['description']);
            $news->setImages($data['image']);
            $news->setDate($data['date']);
            $entityManager->flush();
        }else{
            $news = new News();
            $news->setTitle($data['title']);
            $news->setDescription($data['description']);
            $news->setImages($data['image']);
            $news->setDate($data['date']);
            $news->setCreatedAt(new \DateTimeImmutable());
            $news->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($news);
            $entityManager->flush();
        }
            
    

    
        return new Response('Saved new news with id '.$news->getId());
    }

    #[Route('/news/{id}', name: 'app_news_show')]
    public function getNews($id): Response
    {
        $news = $this->doctrine->getRepository(News::class)->find($id);
    
        if (!$news) {
            throw $this->createNotFoundException(
                'No news found for id '.$id
            );
        }
    
        return new Response('Check out this great news: '.$news->getTitle());
    }

    #[Route('/news/delete/{id}', name: 'app_news_delete')]
    public function delete($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $this->doctrine->getManager();
        $news = $entityManager->getRepository(News::class)->find($id);
    
        if (!$news) {
            throw $this->createNotFoundException(
                'No news found for id '.$id
            );
        }
    
        $entityManager->remove($news);
        $entityManager->flush();
    
        return new Response('News deleted successfully');
    }
}
