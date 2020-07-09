<?php


namespace App\Controller;



use App\Entity\Album;
use App\Entity\Photo;
use App\Form\Type\AlbumType;
use App\Form\Type\MyPhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumsController extends AbstractController
{
    /**
     * @Route("/albums", name="albums_list")
     * @return Response
     */
    public function showAlbums()
    {
        $albums = $this->getDoctrine()
            ->getRepository(Album::class)
            ->findAll();
        return $this->render('albums.html.twig', [
            'title' => 'Список альбомов',
            'albums' => $albums
        ]);
    }

    /**
     * @Route("/album/view/{id}", name="view_album")
     * @param int $id
     * @return Response
     */

    public function viewAlbum(int $id)
    {
        $album = $this->getDoctrine()
            ->getRepository(Album::class)
            ->find($id);

        if (!$album) {
            return $this->albumNotFound($id);
        }

        return $this->render('album/view.html.twig', [
            'title' => 'Просмотр альбома',
            'album' => $album
        ]);
    }

    /**
     * @Route("/album/create", name="create_album")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAlbum(Request $request)
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $album = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($album);
            $entityManager->flush();

            return $this->redirectToRoute('view_album', ['id' => $album->getId()]);
        }

        return $this->render('album/new_album.html.twig', [
            'form' => $form->createView(),
            'title' => 'Добавление альбома'
        ]);
    }

    /**
     * @Route("/album/change/{id}", name="change_album")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function changeAlbum(Request $request, int $id)
    {
        $album = $this->getDoctrine()
            ->getRepository(Album::class)
            ->find($id);

        if (!$album) {
            return $this->albumNotFound($id);
        }
        $form = $this->createForm(AlbumType::class, $album);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $album = $form->getData();
            $album->setDtChange(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($album);
            $entityManager->flush();

            return $this->redirectToRoute('view_album', ['id' => $album->getId()]);
        }

        return $this->render('album/change_album.html.twig', [
            'form' => $form->createView(),
            'title' => 'Изменение альбома',
            'album' => $album
        ]);
    }

    /**
     * @Route("/album/add_photo/{id}", name="add_photo")
     * @param Request $request
     * @param int $id id альбома
     * @return RedirectResponse|Response
     */
    public function addPhoto(Request $request, int $id)
    {
        $album = $this->getDoctrine()
            ->getRepository(Album::class)
            ->find($id);

        if (!$album) {
            return $this->albumNotFound($id);
        }

        $photo = new Photo();
        $form = $this->createForm(MyPhotoType::class, $photo);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->getData();
            $photo->setAlbumId($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($photo);
            $entityManager->flush();

            return $this->redirectToRoute('view_album', ['id' => $id]);
        }

        return $this->render('album/new_photo.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
            'album' => $album,
            'title' => 'Добавить фотографию'
        ]);

    }

    /**
     * @param int $id
     * @return Response
     */
    public function albumNotFound(int $id)
    {
        return $this->render(
            'error.html.twig', [
            'title' => 'Альбом не найден',
            'id' => $id
        ],
            new Response('', 404)
        );

    }

}