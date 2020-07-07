<?php


namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AlbumsController extends AbstractController
{
    /**
     * @Route("/albums", name="albums_list")
     */
    public function showAlbums()
    {
        return $this->render('albums.html.twig', [
            'title' => 'Список альбомов'
        ]);
    }

}