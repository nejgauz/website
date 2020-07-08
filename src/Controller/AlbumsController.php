<?php


namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumsController extends AbstractController
{
    /**
     * @Route("/albums", name="albums_list")
     * * @return Response
     */
    public function showAlbums()
    {
        return $this->render('albums.html.twig', [
            'title' => 'Список альбомов'
        ]);
    }

    /**
     * @Route("/album/view", name="view_album")
     * @return Response
     */

    public function viewAlbum()
    {
        return $this->render('album/view.html.twig', [
            'title' => 'Просмотр альбома'
        ]);
    }

}