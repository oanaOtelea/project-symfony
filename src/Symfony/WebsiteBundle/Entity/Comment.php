<?php

namespace Symfony\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="Symfony\WebsiteBundle\Entity\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
	/**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    protected $comment;

     /**
     * @var integer
     *
     * @ORM\Column(name="userIdSend", type="integer")
     */
    protected $userIdSend;

    /**
     * @var string
     *
     * @ORM\Column(name="usernameSend", type="string")
     */
    protected $usernameSend;

     /**
     * @var integer
     *
     * @ORM\Column(name="pictureId", type="integer")
     */
    protected $pictureId;

     /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="postDate")
     */
    protected $postDate;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set userIdSend
     *
     * @param integer $userIdSend
     * @return Comment
     */
    public function setUserIdSend($userIdSend)
    {
        $this->userIdSend = $userIdSend;

        return $this;
    }

    /**
     * Get userIdSend
     *
     * @return integer 
     */
    public function getUserIdSend()
    {
        return $this->userIdSend;
    }

    /**
     * Set usernameSend
     *
     * @param string $usernameSend
     * @return Comment
     */
    public function setUsernameSend($usernameSend)
    {
        $this->usernameSend = $usernameSend;

        return $this;
    }

    /**
     * Get usernameSend
     *
     * @return string 
     */
    public function getUsernameSend()
    {
        return $this->usernameSend;
    }

    /**
     * Set pictureId
     *
     * @param integer $pictureId
     * @return Comment
     */
    public function setPictureId($pictureId)
    {
        $this->pictureId = $pictureId;

        return $this;
    }

    /**
     * Get pictureId
     *
     * @return integer 
     */
    public function getPictureId()
    {
        return $this->pictureId;
    }

    /**
    * @ORM\PrePersist
    */
    public function setPostDate()
    {
        $this->postDate = new \DateTime();

    }

    /**
     * Get postDate
     *
     * @return \DateTime 
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

}
