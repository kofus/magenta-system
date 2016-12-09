<?php
 
namespace Kofus\User\Entity;


use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\NodeInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE") 
 * @ORM\Table(name="kofus_user_accounts")
 */
class AccountEntity implements NodeInterface
{
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;
    
    public function getId()
    {
    	return $this->id;
    }
    
    public function setId($value)
    {
        $this->id = $value; return $this;
    }
    

    /**
     * @ORM\Column(nullable=true)
     */
    protected $name;
    
    public function setName($value)
    {
        $this->name = $value; return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $status = 0;
    
    public static $STATUS = array(
    	1 => 'active',
        0 => 'not yet activated',
        -1 => 'blocked'
    );
    
    public function setStatus($value)
    {
    	$this->status = $value; return $this;
    }
    
    public function getStatus($pretty=false)
    {
        if ($pretty)
            return self::$STATUS[$this->status];
    	return $this->status;
    }
    
    
    /**
     * @ORM\Column(type="json_array")
     */
    protected $variables = array();
    
    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value; return $this;
    }
    
    public function getVariable($key, $defaultValue=null)
    {
        if (isset($this->variables[$key]))
            return $this->variables[$key];
        return $defaultValue;
    }
    
    /**
     * @ORM\Column()
     */
    protected $role = 'Member';
    
    public function setRole($value)
    {
        $this->role = $value; return $this;
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function getNodeType()
    {
    	return 'U';
    }
    
    public function __toString()
    {
    	return $this->getName() . ' (' . $this->getNodeId() . ')';
    }
    
    public function getNodeId()
    {
        return $this->getNodeType() . $this->getId();
    }
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $timestampCreated;
    
    public function setTimestampCreated(\DateTime $datetime)
    {
    	$this->timestampCreated = $datetime; return $this;
    }
    
    public function getTimestampCreated()
    {
        return $this->timestampCreated;
    }
    
    
    
    
    /*
    public function getArrayCopy()
    {
        return array();
    }
    
    public function populate()
    {
        return $this;
    } */
    
    
}
