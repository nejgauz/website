<?php


namespace App\Controller;



use App\Entity\Album;
use App\Entity\Photo;
use App\Form\Type\AlbumType;
use App\Form\Type\MyPhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
            'albums' => $albums,
        ]);
    }

    /**
     * @Route("/album/view/{id}", name="view_album")
     * @param int $id
     * @param Request $request
     * @return Response
     */

    public function viewAlbum(int $id, Request $request)
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

        return $this->render('album/view.html.twig', [
            'title' => 'Просмотр альбома',
            'album' => $album,
            'form' => $form->createView()
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

            return $this->redirectToRoute('albums_list');
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

            return $this->redirectToRoute('albums_list');
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
            $imageFile = $form->get('file')->getData();

            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            $directory = __DIR__ . '/../../public/assets/img';

            try {
                $imageFile->move(
                    $directory,
                    $newFilename
                );
            } catch (FileException $e) {
                return $this->render(
                    'error.html.twig', [
                    'title' => 'Ошибка загрузки',
                    'message' => 'Не удалось загрузить изображение'
                ]);
            }

            $photo->setImagePath($newFilename);
            $photo->setAlbum($album);
            $photo = $form->getData();

            $album->setDtChange(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($photo);
            $entityManager->persist($album);
            $entityManager->flush();
        }

        $errors = $this->getErrorMessages($form);

        $response = new Response();
        $response->setContent(json_encode(['errors' => $errors]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * @Route("/album/delete/{id}", name="delete_album")
     * @param int $id
     * @return Response
     */
    public function deleteAlbum(int $id)
    {
        $album = $this->getDoctrine()
            ->getRepository(Album::class)
            ->find($id);

        if (!$album) {
            return $this->albumNotFound($id);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($album);
        $entityManager->flush();

        return $this->redirectToRoute('albums_list');

    }

    /**
     * @Route("/photo/delete/{id}", name="delete_photo")
     * @param int $id
     * @return Response
     */
    public function deletePhoto(int $id)
    {
        $photo = $this->getDoctrine()
            ->getRepository(Photo::class)
            ->find($id);

        if (!$photo) {
            return $this->render(
                'error.html.twig', [
                'title' => 'Фотография не найдена',
                'message' => 'Фотографии с id ' . $id . ' не существует'
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($photo);
        $entityManager->flush();


        return $this->redirectToRoute('view_album', ['id' => $photo->getAlbum()->getId()]);

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
            'message' => "Альбома с id ' . $id . ' не существует"
        ],
            new Response('', 404)
        );

    }
    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }
        //рекурсивный случай
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

}