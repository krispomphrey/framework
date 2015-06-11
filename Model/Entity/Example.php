<?php
/**
 * An example Entity used by Doctrine.
 *
 * Please refer to the Doctrine documentation for further
 * examples: http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html
 */

// All entity namespaces should follow the folder structure.
namespace Model\Entity;

/**
 * @Entity
 */
class Example{

	/**
   * @Id @Column(type="integer")
   * @GeneratedValue
   */
  private $id;

  /**
   * @Column(type="datetime")
   */
  private $created;

  /**
   * @Column(type="datetime")
   */
  private $updated;

  /**
   * @Column(type="boolean")
   */
  private $deleted;

  /**
   * @Column(length=140)
   */
  private $field1;

  /**
   * @Column(type="datetime", name="field_2")
   */
  private $field2;

  /** Get / Set Functions **/
  /**
   * These are used to manipulate and extract
   * data from the object
   */

  public function getId(){
  	return $this->id;
  }

  public function getCreated(){
  	return $this->created;
  }

  public function setCreated($created = null){
  	// If no date is provided, set it as now.
  	if(!$created) $created = new \DateTime('now');

  	$this->created = $created;
  	return $this;
  }

  public function getUpdated(){
  	return $this->updated;
  }

  public function setUpdated($updated = null){
  	// If no date is provided, set it as now.
  	if(!$updated) $updated = new \DateTime('now');

  	$this->updated = $updated;
  	return $this;
  }

  public function getDeleted(){
  	return $this->deleted;
  }

  public function setDeleted($deleted){
  	$this->deleted = $deleted;
  	return $this;
  }

  public function getField1(){
  	return $this->field1;
  }

  public function setField1($field1){
  	$this->field1 = $field1;
  	return $this;
  }

  public function getField2(){
  	return $this->field1;
  }

  public function setField2($field2 = null){
  	// If no date is provided, set it as now.
  	if(!$field2) $field2 = new \DateTime('now');

  	$this->field2 = $field2;
  	return $this;
  }

}
