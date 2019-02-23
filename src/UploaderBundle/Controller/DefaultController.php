<?php

namespace UploaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UploaderBundle\Entity\MediaFile;

/**
 * Class DefaultController
 * @package UploaderBundle\Controller
 * @Route("/media")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="media_files_list")
     */
    public function indexAction()
    {
        $data['files'] = $this->getDoctrine()->getManager()->getRepository(MediaFile::class)->findAll();
        return $this->render('UploaderBundle:Default:index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/delete", name="media_file_delete")
     */
    public function deleteAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $mediaFile = $em->getRepository(MediaFile::class)->find($id);
        if($mediaFile instanceof MediaFile){
            unlink($this->get('kernel')->getProjectDir()."/web".$mediaFile->getFileUrl());
            try{
                $em->remove($mediaFile);
                $em->flush();
                $this->addFlash('success', 'Media file deleted.');
            }   catch (\Throwable $e){
                $this->addFlash('error',$e->getMessage());
            }
        }
        return $this->redirectToRoute('media_files_list');
    }
}
