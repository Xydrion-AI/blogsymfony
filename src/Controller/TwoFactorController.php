<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TwoFactorController extends AbstractController
{
    #[Route('/2fa/google/enable', name: 'app_two_factor')]
    public function enableGoogle2fa(EntityManagerInterface $em, GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Générer le secret
        $secret = $googleAuthenticator->generateSecret();

        // Stocker dans l'utilisateur
        $user->setGoogleAuthenticatorSecret($secret);

        // Sauvegarder en base
        $em->persist($user);
        $em->flush();

        // Renvoyer la clé ou un QR code (généralement pour afficher à l'utilisateur)
        $qrCodeUri = $googleAuthenticator->getQRContent($user);

        return $this->render('security/2fa_google_setup.html.twig', [
            'secret' => $secret,
            'qrCodeUri' => $qrCodeUri,
        ]);
    }

    #[Route('/2fa', name: '2fa_login')]
    public function twoFactorForm(): Response
    {
        return $this->render('security/two_factor_google.html.twig');
    }
}
