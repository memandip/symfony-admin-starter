<?php

namespace UserBundle\Controller;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use UserBundle\Entity\Group;
use UserBundle\Form\GroupType;
use Symfony\Component\HttpFoundation\Request;
use MainBundle\Annotations\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GroupController
 * @package UserBundle\Controller
 */
class GroupController extends Controller
{

    /**
     * @var EntityManager
     * @DI\Inject("doctrine.orm.default_entity_manager")
     */
    private $em;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Permissions("user_group_list", group="user", subGroup="group")
     * @Route("/user/group/list", name="user_group_list")
     */
    public function listAction(Request $request)
    {
        $userGroupRepo = $this->em->getRepository(Group::class);
        $groups = $userGroupRepo->findBy(['deleted' => false]);
        $data['groups'] = $groups;
        $data['page_title'] = "User Group";
        $data['page_desc'] = "List";

        return $this->render('@User/Group/list.html.twig', $data);
    }


    /**
     * @Permissions("user_group_create", group="user", subGroup="group", parent="user_group_list", route="ys_admin_user_group_add")
     * @Permissions("user_group_update", group="user", subGroup="group", parent="user_group_list", route="ys_admin_user_group_update")
     * @Route("/user/group/add", name="ys_admin_user_group_add")
     * @Route("/user/group/update/{id}", name="user_group_update")
     */
    public function createAction(Request $request)
    {

        $user = $this->getUser();
        $data['page_title'] = "User Group";
        $data['page_desc'] = "New";
        $data['permittedPermissions'] = [];
        $isEditing = false;
        if ($request->get('id') and $request->get('_route') == 'user_group_update') {
            $group = $this->em->find(Group::class, $request->get('id'));
            if (!$group) {
                throw new NotFoundHttpException;
            }
            $data['permittedPermissions'] = $group->getPermissions();
            $remarks = 'Updated User Group';
            $data['isUpdate'] = true;
            $data['page_desc'] = "Update";
            $isEditing = true;
        } else {
            $group = new Group();
            $remarks = 'Created User Group';
            $data['isUpdate'] = false;
        }
        $form = $this->createForm(GroupType::class, $group, ['user' => $user]);
        if ("POST" == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $permissions = $data['permittedPermissions'] = isset($_POST['permissions']) ? $_POST['permissions'] : [];
                    if (count($permissions)) {
                        $group = $form->getData();
                        if (!$isEditing) {
                            $name = $form->get('name')->getData();
                            $groupName = $this->get('service.user')->findGroupByName($name);
                            if (!is_null($groupName)) {

                                $this->addFlash('error', 'Group name already exist.');
                                if ($isEditing) {
                                    return $this->redirectToRoute('user_group_update', ['id' => $group->getId()]);
                                }

                                return $this->redirectToRoute('ys_admin_user_group_add');
                            }
                        }
                        $group->setPermissions($permissions);
                        $this->em->persist($group);
                        $this->em->flush();
                        $this->addFlash('success', 'Group saved successfully.');

                        return $this->redirectToRoute('user_group_list');
                    } else {
                        $data['errorMessage'] = 'Please choose permissions.';
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }
            }
        }
        $data['form'] = $form->createView();
        $data['permissions'] = $this->get('service.permission')->getPermissionsList($isEditing ? $group : null);

        return $this->render('@User/Group/create.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Permissions("user_group_delete", group="user", subGroup="group", parent="user_group_list")
     * @Route("/user/group/delete/{id}", name="user_group_delete")
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        $userGroup = $this->em->getRepository(Group::class)->find(['id' => $id]);
        if ($userGroup) {
            $userGroup->setDeleted(true);
            $this->em->persist($userGroup);
            try {
                $this->em->flush();
                $this->addFlash('success', 'User Group Deleted');

                return $this->redirectToRoute('user_group_list');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }
    }

}
