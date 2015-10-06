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
 * @Route("/photo")
 */
class PhotoController extends BaseController
{
    /**
    * @Route("/upload/{id}", name="upload", requirements={"id" = "\d+"})
    * @Method({"GET", "POST"})
    */
   public function uploadAction(Request $request)
   {
    $id = $request->query->get('id');
    $form = $this->createForm(new ImageType(), null);
    $em = $this->getDoctrine()->getManager();
    $userProfile = $em->getRepository('SymfonyWebsiteBundle:User')->find($id);
    $images = $em->getRepository('SymfonyWebsiteBundle:Image')->findByUserId($id);
    
    if ($request->request->get('searched')) {
            $search = $this->searchAction();
            return $search;
    }

    if ($request->getMethod() == "POST") {
            $form->bind($request);
        if ($form->isSubmitted()) {
            $this->setMultipleUpload($form->getData());

            if ( $request->isXmlHttpRequest() ) {

                $array = array( 'success' => true, 'message' => 'information updated successfully.' ); // data to return via JSON
                $response = new Response( json_encode( $array ) );
                $response->headers->set( 'Content-Type', 'application/json' );
                    
                return $response;
            }
        }
    }
    
    return $this->render('website/upload.html.twig', array(
            'form' => $form->createView(),
            'images' => $images,
            'userProfile' => $userProfile

        ));
   }

    private function setMultipleUpload($data)
    {
        $user = $this->getUser();
        $id = $user->getId();
        $i=1;
        foreach ($data['file'] as $item) {
            $image = new Image();
            $image->setUserId($id);
            $image->setFile($item);
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $i++;
        }
        $em->flush();
    }

    /**
    * @Route("/comment/{id}", name="comment", requirements={"id" = "\d+"})
    * @Method({"GET", "POST"})
    */
    public function commentAction(Request $request)
    { 
        $imageId = $request->query->get('id');
        $form = $this->createForm(new ImageType(), null);
        $em = $this->getDoctrine()->getManager();
        $currentPic = $em->getRepository('SymfonyWebsiteBundle:Image')->find($imageId);
        $idOfUserYouSee = $currentPic->getUserId();
        $userProfile = $em->getRepository('SymfonyWebsiteBundle:User')->find($idOfUserYouSee);
        $images = $em->getRepository('SymfonyWebsiteBundle:Image')->findByUserId($idOfUserYouSee);
      
        $user = $this->getUser();
        $userUsername = $user->getUsername();
        $userId = $user->getId();
        $allComments = $em->getRepository('SymfonyWebsiteBundle:Comment')->findByPictureId($imageId);
        $comments = $em->getRepository('SymfonyWebsiteBundle:Comment')->findByPictureId($imageId, array('postDate' => 'DESC'), 4);
        $firstCommentReceived = $em->getRepository('SymfonyWebsiteBundle:Comment')->findByPictureId($imageId, array('id' => 'ASC'), 1, 0);

        $numberOfCommentsFromOnePicture = count($allComments);
        define('NUMBER_OF_COMMENTS_TO_SHOW', '4');
        $numberOfCommentsRemainToShow = $numberOfCommentsFromOnePicture - NUMBER_OF_COMMENTS_TO_SHOW;

        if ($request->request->get('idd')) {
            $firstId = $request->request->get('idd');
            $prevComments = $em->getRepository('SymfonyWebsiteBundle:Comment')->loadPrevComments($imageId, $firstId);

            if ( $request->isXmlHttpRequest() ) {
                if ($firstCommentReceived[0]->getId() != $firstId) {
                    
                    $response = $this->renderView('website/allComments.html.twig', array('comments' => $prevComments));
                    $array = array( 'success' => true, 'template' => $response );
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

        $counts = $em->getRepository('SymfonyWebsiteBundle:Like')->countLikes($imageId, $userId);
        $allLikes = $em->getRepository('SymfonyWebsiteBundle:Like')->loadAllLikes($imageId, $userId);
        $existingLike = $em->getRepository('SymfonyWebsiteBundle:Like')->findOneBy(array('userId' => $userId, 'pictureId' => $imageId));
        
        $formUser = $this->createForm(new ProfilePictureType(), $user);

        if ($request->request->get('profile_picture')) {
            $formUser->bind($request);
            if ($formUser->isSubmitted()) {
                $user->setImageId($imageId);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                if ( $request->isXmlHttpRequest() ) {

                    $array = array( 'success' => true ); // data to return via JSON
                    $response = new Response( json_encode( $array ) );
                    $response->headers->set( 'Content-Type', 'application/json' );
                        
                    return $response;
                }
            }
        }

        $like = new Like();
        $formLike = $this->createForm(new LikeType(), $like);

        if ($request->request->get('idOfImage1')) {
          $formLike->bind($request);
            if ($formLike->isSubmitted()) {
                    $like->setLikeNumber("1");
                    $like->setUsernameSend($userUsername);
                    $like->setUserId($userId);
                    $like->setPictureId($imageId);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($like);
                    $em->flush();
                

                if ( $request->isXmlHttpRequest() ) {
                    $existingLike = $em->getRepository('SymfonyWebsiteBundle:Like')->findOneBy(array('userId' => $userId, 'pictureId' => $imageId));
                    $templateAllLikes = $this->renderView('website/youLikeList.html.twig', array('existingLike' => $existingLike));
                    $templateNumberOfLikes = $this->renderView('website/numberOfLikes.html.twig', array('counts' => $counts, 'existingLike' => $existingLike));
                    $array = array( 'success' => true, 'templateAllLikes' => $templateAllLikes, 'existingLike' => $existingLike, 'templateNumberOfLikes' => $templateNumberOfLikes); // data to return via JSON
                    $response = new Response( json_encode( $array ) );
                    $response->headers->set( 'Content-Type', 'application/json' );
                        
                    return $response;
                }
            }
        }

        if ($request->request->get('idOfImage2')) {
         
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($existingLike);
                    $em->flush(); 
                

                if ( $request->isXmlHttpRequest() ) {
                    $existingLike = $em->getRepository('SymfonyWebsiteBundle:Like')->findOneBy(array('userId' => $userId, 'pictureId' => $imageId));
                    $templateAllLikes = $this->renderView('website/youLikeList.html.twig', array('existingLike' => $existingLike));
                    $templateNumberOfLikes = $this->renderView('website/numberOfLikes.html.twig', array('counts' => $counts, 'existingLike' => $existingLike));
                    $array = array( 'success' => true, 'templateAllLikes' => $templateAllLikes, 'existingLike' => $existingLike, 'templateNumberOfLikes' => $templateNumberOfLikes); // data to return via JSON
                    $response = new Response( json_encode( $array ) );
                    $response->headers->set( 'Content-Type', 'application/json' );
                        
                    return $response;
                }
            }
        
                
        $comment = new Comment();
        $formComment = $this->createForm(new CommentType, $comment);   

        if ($request->request->get('comment')) { 
            $formComment->bind($request);
                if ($formComment->isSubmitted()) {
            
                $comment->setUserIdSend($userId);
                $comment->setPictureId($imageId);
                $comment->setUsernameSend($userUsername);

                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                if ( $request->isXmlHttpRequest() ) {
                    $template = $this->renderView('website/oneComment.html.twig', array('comment' => $comment));
                    $array = array( 'success' => true, 'template' => $template ); // data to return via JSON
                    $response = new Response( json_encode( $array ) );
                    $response->headers->set( 'Content-Type', 'application/json' );
                        
                    return $response;
                }
            }
        }

        return $this->render('website/comment.html.twig', array(
            'form' => $form->createView(),
            'images' => $images,
            'currentPic' => $currentPic,
            'comments' => $comments,
            'userProfile' => $userProfile,
            'formComment' => $formComment->createView(),
            'counts' => $counts,
            'existingLike' => $existingLike,
            'formUser' => $formUser->createView(),
            'numberOfCommentsRemainToShow' => $numberOfCommentsRemainToShow,
            'allLikes' => $allLikes
        ));
}

    

}