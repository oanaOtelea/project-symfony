<?php

namespace Symfony\WebsiteBundle\Controller;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\WebsiteBundle\Entity\User;
use Symfony\WebsiteBundle\Entity\Role;
use Symfony\WebsiteBundle\Entity\Image;
use Symfony\WebsiteBundle\Form\RegisterType;
use Symfony\WebsiteBundle\Entity\Like;
use Symfony\WebsiteBundle\Form\LikeType;
use Symfony\WebsiteBundle\Entity\Comment;
use Symfony\WebsiteBundle\Form\CommentType;
use Symfony\WebsiteBundle\Entity\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @Route("/")
 */
class WebsiteController extends Controller
{
   /**
    * @Route("/", name="index")
    * @Template("SymfonyWebsiteBundle:Website:index.html.twig")
    */
    public function indexAction()
    {
      
        
        $user = $this->getUser();
        if ($user) {
            return $this->redirect($this->generateUrl('homepage'));
        } else {
            return $this->render('website/index.html.twig');
        }
    }

   /**
    * @Route("/logout")
    */
    public function logoutAction() 
    {
        $session = $this->getRequest()->getSession();
        $session->clear();
        $session->invalidate();
        $this->get("security.context")->setToken(null);

        return $this->redirect($this->generateUrl('homepage'));
    }
   
   
   /**
    * @Route("/register", name="register")
    * @Method({"GET", "POST"})
    */
    public function registerAction(Request $request) 
    {
        $register = new User();
        $form = $this->createForm(new RegisterType(), $register);

        $roleUser = $this->getDoctrine()
                     ->getRepository('SymfonyWebsiteBundle:Role')
                     ->find(2); // Finds "ROLE_USER"
        $roles = new \Doctrine\Common\Collections\ArrayCollection;
        $roles->add($roleUser);
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        
            $inputLastname = $form["lastname"]->getData();
            $inputFirstname = $form["firstname"]->getData();
            $inputUsername = $form["username"]->getData();
            $inputEmail = $form["email"]->getData();
            $inputPassword = $form["password"]->getData();
            $inputRepeatedPassword = $form["repeatedPassword"]->getData();
            $inputAgree = $form["agree"]->getData();
            $em = $this->getDoctrine()->getManager();
            $user1 = $em->getRepository('SymfonyWebsiteBundle:User')->loadUserByUsername($inputUsername);
            $user2 = $em->getRepository('SymfonyWebsiteBundle:User')->loadUserByUsername($inputEmail);
             
            if ($form->isSubmitted() && $user1 == false && $user2 == false && !empty($inputUsername) && !empty($inputEmail) && !empty($inputPassword) && !empty($inputLastname) && !empty($inputFirstname) && !empty($inputRepeatedPassword) && !empty($inputAgree) && $inputPassword == $inputRepeatedPassword && $inputAgree == true && filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {

                $register->setPassword($this->encodePassword($register, $register->getPassword()));
                $register->setImageId('0');
                $register->setRoles($roles);

                $em = $this->getDoctrine()->getManager();
                $em->persist($register);
                $em->flush();

                    if ( $request->isXmlHttpRequest() ) {

                        $array = array( 'success' => true, 'message' => 'Registered successfully' ); // data to return via JSON
                        $response = new Response( json_encode( $array ) );
                        $response->headers->set( 'Content-Type', 'application/json' );
                                
                        return $response;
                    }
                } else {

                    if (empty($inputUsername)) {
                        $emptyUsername = 'This field should not be empty!';
                    } else {
                        $emptyUsername = null;
                    }

                    if (empty($inputEmail)) {
                        $emptyEmail = 'This field should not be empty!';
                    } else {
                        $emptyEmail = null;
                    }

                    if (empty($inputFirstname)) {
                        $emptyFirstname = 'This field should not be empty!';
                    } else {
                        $emptyFirstname = null;
                    }

                    if (empty($inputLastname)) {
                        $emptyLastname = 'This field should not be empty!';
                    } else {
                        $emptyLastname = null;
                    }

                    if (empty($inputPassword)) {
                        $emptyPassword = 'This field should not be empty!';
                    } else {
                        $emptyPassword = null;
                    }

                    if (empty($inputRepeatedPassword)) {
                        $emptyRepeatedPassword = 'This field should not be empty!';
                    } else {
                        $emptyRepeatedPassword = null;
                    }

                    if ($inputAgree == false) {
                        $errorAgree = 'Check the box if you agree with the terms and conditions!';
                    } else {
                        $errorAgree = null;
                    }
                        
                    if (!filter_var($inputEmail, FILTER_VALIDATE_EMAIL) && !empty($inputEmail)) {
                        $emailNotValid = 'This email adress is not valid!'; 
                    } else {
                        $emailNotValid = null;
                    }

                    if ( $inputPassword != $inputRepeatedPassword && !empty($inputPassword) && !empty($inputRepeatedPassword) ){
                        $errorNotMatch = "Passwords don't match!";
                    } else {
                        $errorNotMatch = null;
                    }

                    if ($user1 == true || $user2 == true) {
                        $errorUser = "This username/email is already registered!";
                    } else {
                        $errorUser = null;
                    }

                    $errors = array('emptyUsername' => $emptyUsername, 'emptyEmail' => $emptyEmail, 'emptyLastname' => $emptyLastname, 'emptyFirstname' => $emptyFirstname, 'emptyPassword' => $emptyPassword, 'emptyRepeatedPassword' => $emptyRepeatedPassword, 'emailNotValid' => $emailNotValid, 'agree' => $errorAgree, 'passwordMatch' => $errorNotMatch, 'user' => $errorUser); 
                        
                        if ( $request->isXmlHttpRequest() ) {

                            $array = array( 'success' => false, 'message' => $errors ); // data to return via JSON
                            $response = new Response( json_encode( $array ) );
                            $response->headers->set( 'Content-Type', 'application/json' );
                                    
                            return $response;
                        }
                }
        }

    
        return $this->render('website/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

   /**
    * @Route("/show", name="show")
    */
    public function showAction() 
    {
        $user = $this->getUser();
        $id = $user->getId();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === true) {
            return $this->redirect($this->generateUrl('admin'));
        } else {
            return $this->redirect($this->generateUrl('profile', array('id' => $id)));
        }
        
    }

   /**
    * @Route("/search", name="search")
    * @Method({"GET", "POST"})
    */
    public function searchAction(Request $request) {
        $username = $request->request->get('search');
        $button = $request->request->get('submit');
        $em = $this->getDoctrine()->getManager();
        $userProfile = $em->getRepository('SymfonyWebsiteBundle:User')->loadUserByUsername($username);
        $id = $userProfile->getId();

        return $this->redirect($this->generateUrl('profile', array('id' => $id)));
    }

   /**
    * @Route("/login")
    * @Method({"POST"})
    */
    public function loginAction(Request $request)
    {
        // get the error if any (works with forward and redirect -- see below) 
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('website/index.html.twig', array(
            // last username entered by the user
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

     private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }


    /**
    * @Route("/admin")
    */
    public function adminAction()
    {
        return $this->render('website/admin.html.twig');
    }

     
}