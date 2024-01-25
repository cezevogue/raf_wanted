<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/security')]
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/signup', name: 'app_signup')]
    public function signup(EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $pass, MailerInterface $mailer): Response
    {

        $user = new User();
        // génération du formulaire à partir de la classe UserType(qui est lié à la classe User)
        $form = $this->createForm(UserType::class, $user);

        // ici on va gérer la requête entrante
        $form->handleRequest($request);

        // si le form est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {


            // Encode le mot de passe
            $user->setPassword($pass->hashPassword($user, $user->getPassword()));

            // set de sa prop active à 0
            $user->setActive(0);

            // on appelle la méthode generateToken juste en dessous pourngénérer une chaine de caractère aléatoire et unique
            $token = $this->generateToken();

            // on l'affecte à notre utilisateur
            $user->setToken($token);

            // on persiste les valeurs
            $manager->persist($user);

            // on exécute la transaction
            $manager->flush();

            // message de confirmation
            $this->addFlash('success', 'Votre compte a bien été créé, allez vite l\'activer');

            // on injecte la dépendance mailer
            $email = (new TemplatedEmail())
                ->from('cezdesaulle@gmail.com')
                ->to($user->getEmail())
                ->subject('Bienvenue chez Re-Wanted!')

                // path of the Twig template to render
                ->htmlTemplate('email/validateAccount.html.twig')

                // change locale used in the template, e.g. to match user's locale
                // ->locale('fr')

                // pass variables (name => value) to the template
                ->context([
                    'user' => $user
                ]);
            $mailer->send($email);
            // ensuite on redirige vers la route app_login
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signup.html.twig', [
            'form' => $form->createView()
        ]);
        return $this->render('security/signup.html.twig');
    }


    private function generateToken()
    {
        // rtrim supprime les espaces en fin de chaine de caractère
        // strtr remplace des occurences dans une chaine ici +/ et -_ (caractères récurent dans l'encodage en base64) par des = pour générer des url valides
        // ce token sera utilisé dans les envoie de mail pour l'activation du compte ou la récupération de mot de passe
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }


    // méthode d'entrée au click du mail de validation du compte
    #[Route('/validate-account/{token}', name: 'validate_account')]
    public function validate_account($token, UserRepository $repository, EntityManagerInterface $manager): Response
    {

        // on va requeter un user sur son token
        $user = $repository->findOneBy(['token' => $token]);

        // si on a un résultat, on passe sa propriété active à 1 , son token à null et on persist, execute et redirige sur la page de connexion avec un message de success

        if ($user) {

            $user->setToken(null);
            $user->setActive(1);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Félicitation votre compte est à présent actif, connectez-vous!!');
        } else {

            $this->addFlash('danger', 'Une erreur s\'est produite');
        }

        return $this->redirectToRoute('app_login');


    }


    // méthode pour mot de passe oublié pour accéder au formulaire demandant la saisie de l'email et générer l'envoie d'un mail de réinitialisation

    #[Route('/reset-password', name: 'reset_password')]
    public function reset_password(Request $request, UserRepository $repository, EntityManagerInterface $manager): Response
    {

        // recupération de la saisie formulaire
        $email = $request->request->get('email', '');

        if (!empty($email)) {
            // requete de user par son email
            $user = $repository->findOneBy(['email' => $email]);

            // si on a utilisateur et que son compte est actif on procède à l'envoie de l'email de récupération
            if ($user && $user->getActive() == 1) {
                // on génère un token
                $user->setToken($this->generateToken());
                $manager->persist($user);
                $manager->flush();

                // reste à générer l'envoi d'email


            }


        }

        return $this->render('security/reset_password.html.twig', [

        ]);
    }


    #[Route('/new-password/{token}', name: 'new_password')]
    public function new_password(): Response
    {
        //requete $user par token findOneBy()
        $user=null;
        $form=$this->createForm(NewPasswordType::class, $user);


        return $this->render('security/new_password.html.twig', [

        ]);
    }

}
