<?php

namespace Symfony\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity
 * @ORM\Table(name="tablelikes")
* @ORM\Entity(repositoryClass="Symfony\WebsiteBundle\Entity\LikeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Like
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
     * @var integer
     *
     * @ORM\Column(name="likeNumber", type="integer")
     */
    protected $likeNumber;

     /**
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer")
     */
    protected $userId;

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
     * @var string
     *
     * @ORM\Column(name="usernameSend", type="string")
     */
    protected $usernameSend;

    public function __construct() 
    {
        $this->likeNumber = 1;
    }

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
     * Set userId
     *
     * @param integer $userId
     * @return Like
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set pictureId
     *
     * @param integer $pictureId
     * @return Like
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

    /**
     * Set likeNumber
     *
     * @param integer $likeNumber
     * @return Like
     */
    public function setLikeNumber($likeNumber)
    {
        $this->likeNumber = $likeNumber;

        return $this;
    }

    /**
     * Get likeNumber
     *
     * @return integer 
     */
    public function getLikeNumber()
    {
        return $this->likeNumber;
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
}
