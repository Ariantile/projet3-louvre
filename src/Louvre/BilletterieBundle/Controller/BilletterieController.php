<?php
// src/Louvre/BilletterieBundle/Controller/BilletterieController.php

namespace Louvre\BilletterieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Louvre\BilletterieBundle\Entity\Quantite;
use Louvre\BilletterieBundle\Entity\Tarifs;
use Louvre\BilletterieBundle\Entity\Facturation;
use Louvre\BilletterieBundle\Entity\Billet;
use Louvre\BilletterieBundle\Entity\Commande;
use Louvre\BilletterieBundle\Form\QuantiteType;
use Louvre\BilletterieBundle\Form\TarifsType;
use Louvre\BilletterieBundle\Form\FacturationType;
use Louvre\BilletterieBundle\Form\BilletType;
use Louvre\BilletterieBundle\Form\CommandeType;
use Payum\Core\Payum;
use Payum\Core\Security\SensitiveValue;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Bridge\Symfony\Form\Type\CreditCardType;
use Payum\Core\Model\CreditCardInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Request\GetHumanStatus;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Stripe\Request\Api\CreatePlan;

class BilletterieController extends Controller
{
    public function formaccAction()
    {        
        $repository = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('LouvreBilletterieBundle:Tarifs');

        $listTarifs = $repository->findAll();

        $commande = new Commande();
        $formCommande = $this->get('form.factory')->create(CommandeType::class, $commande);
        
        $date = new \DateTime('now');
        
        return $this->render('LouvreBilletterieBundle:Billetterie:formacc.html.twig', array(
                'formcommande'  => $formCommande->createView(),
                'listTarifs'    => $listTarifs,
                'dateConnexion' => $date,
        ));
    }
    
    public function coordonneesAction(Request $request)
    {
        $facturation = new Facturation();
        $formFacturation = $this->get('form.factory')->create(FacturationType::class, $facturation);
        
        $billet = new Billet();
        $formBillet = $this->get('form.factory')->create(BilletType::class, $billet);
            
        return $this->render('LouvreBilletterieBundle:Billetterie:coordonnees.html.twig', array(
            'formfacturation'   => $formFacturation->createView(),
            'formbillet'        => $formBillet->createView(),
        ));    
    
    }
    
    public function paiementAction()
    {
        return $this->render('LouvreBilletterieBundle:Billetterie:paiement.html.twig');
    }
    
    public function prepareStripeAction(Request $request)
    {
        $gatewayName = 'louvre_stripe_checkout';

        $storage = $this->getPayum()->getStorage('Louvre\BilletterieBundle\Entity\Payment');

        /** @var Payment $details */
        $details = $storage->create();
        $details["amount"] = 100;
        $details["currency"] = 'EUR';
        $details["description"] = 'a description';
        $storage->update($details);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $details,
            'louvre_payment_done'
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    
    public function doneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $identity = $token->getDetails();
        $model = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // you can invalidate the token. The url could not be requested any more.
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have token you can get the model from the storage directly. 
        //$identity = $token->getDetails();
        //$details = $payum->getStorage($identity->getClass())->find($identity);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $details = $status->getFirstModel();

        // you have order and payment status 
        // so you can do whatever you want for example you can just print status and payment details.

        return new JsonResponse(array(
            'status' => $status->getValue(),
            'details' => iterator_to_array($details),
        ));
    }

    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }
    
}
