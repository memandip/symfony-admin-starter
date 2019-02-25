<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Form\UserType;

class DefaultController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/profile/update",name="user_profile")
     * @Breadcrumb("Profile Update")
     */
    public function profileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user,['isUpdating' => true]);
        $form->handleRequest($request);
        $userManager = $this->get('fos_user.user_manager');

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            if($user->getPlainPassword()){
                $userManager->updateUser($user);
            }   else    {
                $userManager->updateCanonicalFields($user);
            }

            try{
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Profile updated.');
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
        }
        $data['form'] = $form->createView();
        return $this->render('@User/Default/form.html.twig', $data);
    }

}
