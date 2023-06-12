<?php

namespace App\Security;

use App\Entity\Usuarios;
use App\Repository\UsuariosRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppLoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $userRepo;

    public function __construct(private UrlGeneratorInterface $urlGenerator,UsuariosRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

         $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username,function($userIdentifier) {
                // optionally pass a callback to load the User manually
                $foundUser = $this->userRepo->findOneBy(['username' => $userIdentifier]);
                if (!$foundUser) {
                    throw new UserNotFoundException();
                }
                if (!$foundUser->isVerified()) {
                    throw new CustomUserMessageAccountStatusException('Tu cuenta no ha sido activada todavia.');
                }
    
                return $foundUser;
            }),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('index'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    // public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    // {
    //     $user = $token->getUser();
    
    //     // Verificar si el usuario está verificado
    //     if ($user instanceof Usuarios && ($user->isVerified() == 1 ||$user->isVerified() == True)) {
    //         if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
    //             return new RedirectResponse($targetPath);
    //         }
    
    //         return new RedirectResponse($this->urlGenerator->generate('index'));
    //     }
    
    //     // Usuario no verificado, mostrar mensaje de error
    //     $request->getSession()->set(Security::AUTHENTICATION_ERROR, 'Tu cuenta no ha sido verificada.');
    
    //     return new RedirectResponse($this->urlGenerator->generate('index'));
    // }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
