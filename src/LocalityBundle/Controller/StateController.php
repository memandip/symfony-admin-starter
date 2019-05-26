<?php

namespace LocalityBundle\Controller;

use LocalityBundle\Entity\State;
use LocalityBundle\Form\StateType;
use MainBundle\Annotations\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class StateController
 * @package LocalityBundle\Controller
 * @Breadcrumb("State", routeName="state_list")
 */
class StateController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/states",name="state_list")
     * @Route("/state/{id}/update", name="state_update")
     * @Permissions("list_states", group="state",desc="list all states")
     * @Permissions("create_state", group="state",desc="create state")
     * @Permissions("update_state", group="state",desc="update state")
     */
    public function listAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $message = "State created.";
        $isUpdating = false;

        if($id){
            $state = $em->getRepository(State::class)->findOneBy([
                'deleted' => false,
                'id' => $id
            ]);
            if(! $state instanceof State){
                return $this->redirectToRoute('state_list');
            }
            $message = "State updated.";
            $this->get('apy_breadcrumb_trail')
                ->add($state->getName())
                ->add('Update');
            $isUpdating = true;
        }   else    {
            $state = new State();
        }

        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();
            try{
                $em->persist($formData);
                $em->flush();
                $this->addFlash('success', $message);
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
            return $this->redirectToRoute('state_list');
        }
        $data['isUpdating'] = $isUpdating;
        $data['states'] = $em->getRepository(State::class)->findBy(['deleted' => false]);
        $data['form'] = $form->createView();
        return $this->render('@Locality/state.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/state/{id}/delete", name="state_delete")
     * @Permissions("delete_state", group="state", desc="Delete state")
     */
    public function deleteAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $state = $em->getRepository(State::class)->find($id);

        if($state instanceof State){
            $state->setDeleted(true);
            try{
                $em->persist($state);
                $em->flush();
                $this->addFlash('success','State deleted.');
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('state_list');
    }

}
