<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ParserHistory
 *
 * @ORM\Table( name="parser_history" )
 * @ORM\Entity( repositoryClass="AppBundle\Repository\ParserHistoryRepository" )
 */
class ParserHistory {
	/**
	 * @var int
	 *
	 * @ORM\Column( name="id", type="integer" )
	 * @ORM\Id
	 * @ORM\GeneratedValue( strategy="AUTO" )
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column( name="domain_name", type="string", length=100 )
	 * @Assert\NotBlank( message="Обязательное для заполнения поле" )
	 * @Assert\Regex(
	 *     pattern="/^[a-zA-Z0-9][a-zA-Z0-9-_]{0,61}[a-zA-Z0-9]{0,1}\.([a-zA-Z]{1,6}|[a-zA-Z0-9-]{1,30}\.[a-zA-Z]{2,3})$/",
	 *
	 *     message="Введите корректный домен"
	 * )
	 */
	protected $domainName;

	/**
	 * @var string
	 *
	 * @ORM\Column( name="keyword", type="string", length=50, nullable=true )
	 * @Assert\NotBlank( message="Обязательное для заполнения поле" )
	 */
	protected $keyword;

	/**
	 * @var int
	 *
	 * @ORM\Column( name="position", type="integer", nullable=true )
	 */
	protected $position;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column( name="sample_date", type="datetime", nullable=true )
	 */
	protected $sampleDate;

	public function __construct()
	{
		$this->sampleDate = new \DateTime( 'now' );
	}


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set domainName
	 *
	 * @param string $domainName
	 *
	 * @return ParserHistory
	 */
	public function setDomainName( $domainName )
	{
		$this->domainName = $domainName;

		return $this;
	}

	/**
	 * Get domainName
	 *
	 * @return string
	 */
	public function getDomainName()
	{
		return $this->domainName;
	}

	/**
	 * Set keyword
	 *
	 * @param string $keyword
	 *
	 * @return ParserHistory
	 */
	public function setKeyword( $keyword )
	{
		$this->keyword = $keyword;

		return $this;
	}

	/**
	 * Get keyword
	 *
	 * @return string
	 */
	public function getKeyword()
	{
		return $this->keyword;
	}

	/**
	 * Set position
	 *
	 * @param integer $position
	 *
	 * @return ParserHistory
	 */
	public function setPosition( $position )
	{
		$this->position = $position;

		return $this;
	}

	/**
	 * Get position
	 *
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Get sampleDate
	 *
	 * @return \DateTime
	 */
	public function getSampleDate()
	{
		return $this->sampleDate;
	}
}

