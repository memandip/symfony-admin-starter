<?php

namespace LocalityBundle\Controller;

use LocalityBundle\Entity\State;
use LocalityBundle\Form\StateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class StateController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/states",name="state_list")
     * @Route("/state/{id}/update", name="state_update")
     */
    public function listAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $message = "State created.";

        if($id){
            $state = $em->getRepository(State::class)->findBy([
                'deleted' => false,
                'id' => $id
            ]);
            if(! $state instanceof State){
                return $this->redirectToRoute('state_list');
            }
            $message = "State updated.";
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
        $data['states'] = $em->getRepository(State::class)->findBy(['deleted' => false]);
        $data['form'] = $form->createView();
        return $this->render('@Locality/state.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/state/{id}/delete", name="state_delete")
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
