<?php

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CRUDController
 * @package AdminBundle\Controller
 */
class CRUDController extends Controller
{
    //TODO: add translations for string values

    /**
     * Move item up by decrementing order_num value
     *
     * @return RedirectResponse
     */
    public function moveUpAction()
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Unable to find the object with id : %s', $id));
        }

        $object->setOrderNum($object->getOrderNum() - 1);
        $this->admin->update($object);
        $this->addFlash('sonata_flash_success', 'Moved successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * Move item down by incrementing order_num value
     *
     * @return RedirectResponse
     */
    public function moveDownAction()
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('Unable to find the object with id : %s', $id));
        }

        $object->setOrderNum($object->getOrderNum() + 1);
        $this->admin->update($object);
        $this->addFlash('sonata_flash_success', 'Moved successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * {@inheritdoc}
     */
    public function batchActionDelete(ProxyQueryInterface $query)
    {
        if (false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $res = $query->execute();

        if (count($res)) {
            foreach ($res as $sqTeamEntity) {
                $this->admin->delete($sqTeamEntity, false);
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list',
                $this->admin->getFilterParameters())
        );
    }
}
