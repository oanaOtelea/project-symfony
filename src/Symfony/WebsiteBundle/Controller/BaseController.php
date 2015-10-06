<?php

namespace Symfony\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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


class BaseController extends Controller
{
   
   public function searchAction()
   {  
        $request = Request::createFromGlobals();  
        $em = $this->getDoctrine()->getManager();
        $searched = $request->request->get('searched');
        $searchedUsers = $em->getRepository('SymfonyWebsiteBundle:User')->searchByUsername($searched);

            if (!empty($searchedUsers)) {
                if ( $request->isXmlHttpRequest() ) {
                 
                $template = $this->renderView('website/popUpWithSearchedUsers.html.twig', array('searchedUsers' => $searchedUsers));
                    
                $array = array( 'success' => true, 'template' => $template );
                $res = new Response(json_encode($array));
                $res->headers->set( 'Content-Type', 'application/json' );

                return $res;
                    
                }
            } else {
                if ( $request->isXmlHttpRequest() ) {

                    $array = array( 'success' => false, 'message' => 'No users found'); // data to return via JSON
                    $response = new Response( json_encode( $array ) );
                    $response->headers->set( 'Content-Type', 'application/json' );
                    
                    return $response;
                }
            }
        
    }

   
}