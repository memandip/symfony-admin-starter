<?php

namespace UploaderBundle\Controller;

use UploaderBundle\Entity\MediaFile;
use UploaderBundle\Form\MediaFileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 * @package UploaderBundle\Controller
 * @Route("/media")
 * @Breadcrumb("Media")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="media_files_list")
     */
    public function indexAction()
    {
        $data['medias'] = $this->getDoctrine()->getManager()->getRepository(MediaFile::class)->findAll();
        return $this->render('UploaderBundle:Default:index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/delete", name="media_file_delete")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $mediaFile = $em->getRepository(MediaFile::class)->find($id);
        if ($mediaFile instanceof MediaFile) {
            unlink($this->get('kernel')->getProjectDir() . "/web" . $mediaFile->getFileUrl());
            try {
                $em->remove($mediaFile);
                $em->flush();
                $this->addFlash('success', 'Media file deleted.');
            } catch (\Throwable $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('media_files_list');
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/update", name="media_file_update")
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $response = [];

        if (!$id || !is_numeric($id)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid request.'
            ]);
        }

        $mediaFile = $em->getRepository(MediaFile::class)->find($id);
        if (!$mediaFile instanceof MediaFile) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid request.'
            ]);
        }

        $form = $this->createForm(MediaFileType::class, $mediaFile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mediaFile = $form->getData();
            try{
                $em->persist($mediaFile);
                $em->flush();
                $this->addFlash('success', 'Media updated.');
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
            return $this->redirectToRoute('media_files_list');
        }   else    {
            $data['form'] = $form->createView();
            $response['template'] = $this->renderView('@Uploader/Default/form.html.twig', $data);
        }
        $response['success'] = true;
        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/render", name="media_ajax_template")
     */
    public function renderMedias(Request $request)
    {
        $data['medias'] = $this->getDoctrine()->getManager()->getRepository(MediaFile::class)->findAll();
        $template = $this->renderView('@Uploader/partials/mediaList.html.twig', $data);
        return new JsonResponse([
            'success' => true,
            'template' => $template
        ]);
    }

}
