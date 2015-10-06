<?php

namespace Symfony\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="Symfony\WebsiteBundle\Entity\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="sentDate")
     */
    private $sentDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="userIdSend", type="integer")
     */
    protected $userIdSend;

    /**
     * @var integer
     *
     * @ORM\Column(name="userIdReceive", type="integer")
     */
    protected $userIdReceive;
    
    /**
     * @var string
     *
     * @ORM\Column(name="usernameMessageSend", type="string")
     */
    protected $usernameMessageSend;

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
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
    * @ORM\PrePersist
    */
    public function setSentDate()
    {
        $this->sentDate = new \DateTime();

    }



    /**
     * Get sentDate
     *
     * @return \DateTime 
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set userIdSend
     *
     * @param integer $userIdSend
     * @return Message
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
     * Set userIdReceive
     *
     * @param integer $userIdReceive
     * @return Message
     */
    public function setUserIdReceive($userIdReceive)
    {
        $this->userIdReceive = $userIdReceive;

        return $this;
    }

    /**
     * Get userIdReceive
     *
     * @return integer 
     */
    public function getUserIdReceive()
    {
        return $this->userIdReceive;
    }

     /**
     * Set usernameMessageSend
     *
     * @param string $usernameMessageSend
     * @return Message
     */
    public function setUsernameMessageSend($usernameMessageSend)
    {
        $this->usernameMessageSend = $usernameMessageSend;

        return $this;
    }

    /**
     * Get usernameMessageSend
     *
     * @return string 
     */
    public function getUsernameMessageSend()
    {
        return $this->usernameMessageSend;
    }


}
