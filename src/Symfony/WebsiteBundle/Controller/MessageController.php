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
use Symfony\WebsiteBundle\Form\NewMessageType;
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
 * @Route("/message")
 */
class MessageController extends BaseController
{
    /**
    * @Route("/message")
    * @Method({"GET", "POST"})
    */
    public function messageAction() {
        $user = $this->getUser();
        $id = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('SymfonyWebsiteBundle:Message')->loadConversations($id);
        $idOfRecentConversation = $users[0]['id'];
        return $this->redirect($this->generateUrl('conversations', array('id' => $idOfRecentConversation)));
    }

    /**
    * @Route("/conversations/{id}", name="conversations", requirements={"id" = "\d+"})
    * @Method({"GET", "POST"})
    */
    public function conversationsAction(Request $request) 
    { 
        $user = $this->getUser();
        $username = $user->getUsername();
        $id = $user->getId();
        $em = $this->getDoctrine()->getManager();

        if ($request->request->get('searched')) {
            $search = $this->searchAction();
            return $search;
        }
        
        $users = $em->getRepository('SymfonyWebsiteBundle:Message')->loadConversations($id);
        $photosObjects = array();
        $i = 0;
        foreach ($users as $user) {
            $photos = $em->getRepository('SymfonyWebsiteBundle:Image')->find($users[$i]['image_id']);
            $i++;  
            $photosObjects[] = $photos;     
        } 

        $userInUse = $request->query->get('id');
        $userInConversation = $em->getRepository('SymfonyWebsiteBundle:User')->find($userInUse);
        $firstMessageSent = $em->getRepository('SymfonyWebsiteBundle:Message')->firstMessageSent($userInUse, $id);
        $allMessages = $em->getRepository('SymfonyWebsiteBundle:Message')->loadOneConversation($userInUse, $id);
        $numberOfMessagesInOneConversation = count($allMessages);
        $messages = $em->getRepository('SymfonyWebsiteBundle:Message')->recentMessagesFromOneConversation($userInUse, $id);
        define('NUMBER_OF_MESSAGES_TO_SHOW', '4');
        $numberOfMessagesRemainToShow = $numberOfMessagesInOneConversation - NUMBER_OF_MESSAGES_TO_SHOW;

         if ($request->request->get('idd')) {
            $firstId = $request->request->get('idd');
            $prevMessages = $em->getRepository('SymfonyWebsiteBundle:Message')->loadPrevMessages($userInUse, $id, $firstId);
            
                
            
            if ( $request->isXmlHttpRequest() ) {
                if ($firstMessageSent['id'] != $firstId) {
                 
                    $response = $this->renderView('website/allMessages.html.twig', array('messages' => $prevMessages));
                    
                    $array = array( 'success' => true, 'message' => $response );
                    $res = new Response(json_encode($array));
                    $res->headers->set( 'Content-Type', 'application/json' );
                    return $res;
                    
                } else {
                    
                    $array = array( 'success' => false );
                    $res = new Response(json_encode($array));
                    $res->headers->set( 'Content-Type', 'application/json' );
                    return $res;
                    
                }
            
            }
        }

        $message = new Message();
        $form = $this->createForm(new MessageType(), $message);

        if ($request->request->get('message')) {
            $form->bind($request);
        
        if ($form) {

            $message->setUsernameMessageSend($username);
            $message->setUserIdSend($id);
            $message->setUserIdReceive($userInUse);
            $message->setSentDate(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
            
            if ( $request->isXmlHttpRequest() ) {
                $template = $this->renderView('website/oneMessage.html.twig', array('message' => $message));
                $array = array( 'success' => true, 'template' => $template ); // data to return via JSON
                $response = new Response( json_encode( $array ) );
                $response->headers->set( 'Content-Type', 'application/json' );
                        
                return $response;
            }
        }
    }

    if ($request->request->get('idOfUser')) {
        $idOfTheOtherUser = $request->request->get('idOfUser');
        $userInConversation = $em->getRepository('SymfonyWebsiteBundle:User')->find($idOfTheOtherUser);
        $nameOfUserInConversation = $userInConversation->getUsername();
        $otherUserMessages = $em->getRepository('SymfonyWebsiteBundle:Message')->recentMessagesFromOneConversation($idOfTheOtherUser, $id);
        $allMessages = $em->getRepository('SymfonyWebsiteBundle:Message')->loadOneConversation($idOfTheOtherUser, $id);
        $numberOfMessagesInOneConversation = count($allMessages);
        $numberOfMessagesRemainToShow = $numberOfMessagesInOneConversation - NUMBER_OF_MESSAGES_TO_SHOW;   

            if ( $request->isXmlHttpRequest() ) {
                    
                $response = $this->renderView('website/allMessages.html.twig', array('messages' => $otherUserMessages));   
                $array = array( 'success' => true, 'template' => $response, 'numberOfMessagesRemainToShow' => $numberOfMessagesRemainToShow, 'nameOfUserInConversation' => $nameOfUserInConversation);
                $res = new Response(json_encode($array));
                $res->headers->set( 'Content-Type', 'application/json' );
                return $res;
            }     
    }

    if ($request->request->get('idOfUserFromConversation')) {
    
            if ( $request->isXmlHttpRequest() ) {
                    
                $template = $this->renderView('website/newMessage.html.twig');   
                $array = array( 'success' => true, 'template' => $template );
                $res = new Response(json_encode($array));
                $res->headers->set( 'Content-Type', 'application/json' );
                return $res;
            }     
    }

    $newMessage = new Message();
    $formNewMessage = $this->createForm(new NewMessageType(), $newMessage);

    if ($request->request->get('new_message')) {
            $formNewMessage->bind($request);
            $userInserted = $request->request->get('new_message');
            $userReceiver = $em->getRepository('SymfonyWebsiteBundle:User')->findOneByUsername($userInserted["usernameMessageSend"]);
            $userReceiverId = $userReceiver->getId();

        if ($formNewMessage) {

            $newMessage->setUserIdSend($id);
            $newMessage->setUserIdReceive($userReceiverId);
            $newMessage->setSentDate(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($newMessage);
            $em->flush();
            
            if ( $request->isXmlHttpRequest() ) { 
                $array = array( 'success' => true, 'userReceiverId' => $userReceiverId ); // data to return via JSON
                $response = new Response( json_encode( $array ) );
                $response->headers->set( 'Content-Type', 'application/json' );
                        
                return $response;
            }
        }
    }
        
    return $this->render('website/conversations.html.twig', array(
            'form' => $form->createView(),
            'formNewMessage' => $formNewMessage->createView(),
            'users' => $users,
            'userInConversation' =>$userInConversation,
            'messages' => $messages,
            'firstMessageSent' => $firstMessageSent,
            'numberOfMessagesRemainToShow' => $numberOfMessagesRemainToShow,
            'photosObjects' => $photosObjects
        ));
    }

    
}