<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Extension;

use Mby\CommunityBundle\Service\CsrfIntentRepository;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfTokenManagerAdapter;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * MbyTwigExtension extends Twig capabilities.
 *
 */
class MbyTwigExtension extends \Twig_Extension
{
    /** @var CsrfIntentRepository */
    protected $csrfIntentRepo;

    /** @var  CsrfTokenManagerAdapter */
    protected $csrfTokenManager;

    /** @var  SecurityContext */
    protected $securityContext;

    public function __construct(CsrfIntentRepository $csrfIntentRepo,
                                CsrfTokenManagerAdapter $csrfTokenManager,
                                SecurityContextInterface $securityContext)
    {
        $this->csrfIntentRepo = $csrfIntentRepo;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
             new \Twig_SimpleFunction('mby_csrf', array($this, 'renderMbyCsrf')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mby_extension';
    }

    /**
     * Renders a CSRF token.
     *
     * @param string $intention The intention of the protected action.
     *
     * @return string A CSRF token.
     */
    public function renderMbyCsrf($intention)
    {
        $user = $this->securityContext->getToken()->getUser();
        $intent = $this->csrfIntentRepo->generate($user, $intention);

        $token = $this->csrfTokenManager->generateCsrfToken($intent);

        return $token;
    }

}
