<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Entity\User;
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
        return $this->render('@User/Default/profile.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/users", name="users_list")
     * @Breadcrumb("User")
     * @Breadcrumb("List")
     */
    public function userListAction(Request $request){
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $users = $this->get('service.pagination')->paginate(
            $em->getRepository(User::class)->getUsersQuery()
        );
        return $this->render('@User/Default/list.html.twig', compact('users'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/user/create", name="user_create")
     * @Route("/user/{id}/update", name="user_update")
     * @Breadcrumb("User", routeName="users_list")
     */
    public function createAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $isUpdating = false;
        $apy = $this->get('apy_breadcrumb_trail');
        if($id){
            $user = $em->getRepository(User::class)->find($id);
            if(! $user instanceof User){
                return $this->redirectToRoute('users_list');
            }
            $isUpdating = true;
            $apy->add($user->getFullName())->add('Update');
        }   else    {
            $user = new User();
            $apy->add('Create');
        }

        $form = $this->createForm(UserType::class,$user,['isUpdating' => $isUpdating]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $userManager = $this->get('fos_user.user_manager');
            if($user->getPlainPassword()){
                $userManager->updateUser($user);
            }   else    {
                $userManager->updateCanonicalFields($user);
            }
            try{
                $em->persist($user);
                $em->flush();
                $message = $isUpdating ? "User updated." : "User created.";
                $this->addFlash('success', $message);
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
            return $this->redirectToRoute('users_list');
        }
        $data['isUpdating'] = $isUpdating;
        $data['form'] = $form->createView();
        return $this->render('@User/Default/form.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/user/{id}/delete", name="user_delete")
     */
    public function deleteAction(Request $request){
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $user = $em->getRepository(User::class)->find($id);
        if($user instanceof User){
            try{
                $em->remove($user);
                $em->flush();
                $this->addFlash('success', "User deleted.");
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('users_list');
    }

}
