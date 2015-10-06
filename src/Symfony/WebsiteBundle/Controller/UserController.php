<?php

namespace Symfony\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\WebsiteBundle\Entity\User;
use Symfony\WebsiteBundle\Entity\Image;
use Symfony\WebsiteBundle\Entity\Role;
use Symfony\WebsiteBundle\Entity\Like;
use Symfony\WebsiteBundle\Form\LikeType;
use Symfony\WebsiteBundle\Form\ProfilePictureType;
use Symfony\WebsiteBundle\Entity\Comment;
use Symfony\WebsiteBundle\Form\CommentType;
use Symfony\WebsiteBundle\Entity\Message;
use Symfony\WebsiteBundle\Form\MessageType;
use Symfony\WebsiteBundle\Entity\UserRepository;
use Symfony\WebsiteBundle\Form\RegisterType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\WebsiteBundle\Form\UpdateType;
use Symfony\WebsiteBundle\Form\ImageType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;


/**
 * @Route("/account")
 */
class UserController extends BaseController
{

   /**
    * @Route("/homepage/{id}", name="homepage", requirements={"id" = "\d+"})
    */
    public function homepageAction() 
    {
        $user = $this->getUser();
        $id = $user->getId();
        
        return $this->render('website/homepage.html.twig');
    }

    private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

   /**
    * @Route("/profile/{id}", name="profile", requirements={"id" = "\d+"})
    * @Method({"GET", "POST"})
    */
    public function profileAction(Request $request)
    {
        $idOfProfile = $request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $profile = $em->getRepository('SymfonyWebsiteBundle:User')->find($idOfProfile);
        $user = $this->getUser();
        $photoId = $profile->getImageId();
        $photo = $em->getRepository('SymfonyWebsiteBundle:Image')->find($photoId);
        $password = $user->getPassword();
 
        if ($request->request->get('searched')) {
            $search = $this->searchAction();
            return $search;
        }

        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }
        
        $form = $this->createForm(new UpdateType(), $profile);

        if ($request->request->get('symfony_websitebundle_update')) {
            $form->bind($request);

            if ( !empty($form["username"]->getData()) && !empty($form["email"]->getData()) ) {
                $formPassword = $form["password"]->getData();
                $profileImage = $profile->getImageId();
             
                $profile->setImageId($profileImage);
                

                if (is_null($formPassword)) {
                    $profile->setPassword($password);
                } else {
                    $profile->setPassword($this->encodePassword($profile, $profile->getPassword()));
                }
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($profile);
                $em->flush();

                if ( $request->isXmlHttpRequest() ) {

                    $array = array( 'success' => true, 'message' => 'information updated successfully.' ); // data to return via JSON
                    $response = new Response( json_encode( $array ) );

                    $response->headers->set( 'Content-Type', 'application/json' );
                    
                    return $response;
                }
            } else {
                if ( $request->isXmlHttpRequest() ) {

                    $array = array( 'success' => false, 'message' => 'please fill the empty fields'); // data to return via JSON
                    $response = new Response( json_encode( $array ) );
                    $response->headers->set( 'Content-Type', 'application/json' );
                    
                    return $response;
                }
            }
        }

        return $this->render('website/account.html.twig', array(
            'form' => $form->createView(),
            'profile' => $profile,
            'photo' => $photo
        ));
    }
   

}