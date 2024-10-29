<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'security_login'; // Ensure this matches your route name

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        // Get the username and password from the request payload
        $username = $request->request->get('username'); // Use get instead of getPayload() 
        $password = $request->request->get('password'); // Assuming you're getting it from the request body

        // Store the last username in the session
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        // Create and return a new Passport
        return new Passport(
            new UserBadge($username), // Fetches the user by username
            new PasswordCredentials($password), // Credentials for authentication
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')), // CSRF token validation
                new RememberMeBadge(), // Remember me badge for persistent login
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirect to the target path if it exists
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirect to a default route after successful login
        return new RedirectResponse($this->urlGenerator->generate('some_default_route')); // Replace 'some_default_route' with an actual route name
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
