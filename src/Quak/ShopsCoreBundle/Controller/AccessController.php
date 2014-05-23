<?php
namespace Quak\ShopsCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Quak\ShopsCoreBundle\Entity\User;

/**
 * Access Controller class
 */
class AccessController extends Controller
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction()
    {
        $securityContext = $this->get('security.context');

        if ($securityContext->isGranted(User::ROLE_ADMIN)) {
            return $this->redirect(
                $this->generateUrl('quak_shops_admin_index'));
        }

        if ($securityContext->isGranted(User::ROLE_SHOP)) {
            return $this->redirect(
                $this->generateUrl('quak_shops_report_index'));
        }

        return $this->render('QuakShopsCoreBundle:Access:index.html.twig');
    }

    /**
     * Login action
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'QuakShopsCoreBundle:Access:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContextInterface::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }
}
