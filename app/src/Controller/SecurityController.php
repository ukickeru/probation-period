<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private UserRepository $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route(path="/api/login", name="api_login")
     */
    public function apiLogin()
    {
    }

    /**
     * @Route(path="/api/registration", name="api_registration", methods={"POST"})
     */
    public function apiRegistration(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $name = $request->get('name');
        $password = $request->get('password');

        if (null === $email || null === $name || null === $password) {
            return new JsonResponse(
                'To register, you need to specify email, name and password.',
                400
            );
        }

        $userAlreadyExists = $this->userRepository->findOneBy(['email' => $email]);

        if (null !== $userAlreadyExists) {
            return new JsonResponse(
                'User with same email already exists!',
                400
            );
        }

        $user = new User($email, [], new Name($name), [], $password, $this->passwordHasher);

        $this->userRepository->save($user);

        return new JsonResponse('Registration successfully completed! Now you may log in.');
    }

    /**
     * @Route(path="/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route(path="/logout", name="app_logout")
     */
    public function logout(): void
    {
    }
}
