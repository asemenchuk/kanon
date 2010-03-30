<?php
//  implements IControllableProperty
require_once dirname(__FILE__).'/modelExpression.php';
class modelProperty{
	protected $_name = null;
	
	protected $_fieldName = null;
	protected $_size = 255;
	protected $_notNull = true;
	
	protected $_defaultValue = null;
	protected $_initialValue = null;
	protected $_value = null;
	protected $_options = array();
	protected $_model = null;
	/**
	 * @var IPropertyControl
	 */
	protected $_control = null;
	/*protected function _clone(&$var){
		$var = is_object($var)?clone $var:$var;
	}
	public function __clone(){
		$this->_clone($this->_defaultValue);
		$this->_clone($this->_initialValue);
		$this->_clone($this->_value);
		$this->_clone($this->_model);
	}*/
	public function setFieldName($name){
		$this->_fieldName = $name;
	}
	public function getCreateSql(){
		return '`'.$this->_fieldName.'` varchar('.$this->_size.') CHARACTER SET utf8'.($this->_notNull?' NOT NULL':'');
	}
	public function setOptions($options = array()){
		foreach ($options as $k => $v) $this->_options[$k] = $v;
	}
	public function __construct($propertyName){
		$this->_name = $propertyName;
		$this->onConstruct();
	}
	public function onConstruct(){

	}
	public function is($value){
		return new modelExpression($this, '=', $value);
	}
	public function lt($value){
		return new modelExpression($this, '<', $value);
	}
	public function gt($value){
		return new modelExpression($this, '>', $value);
	}
	public function in($value){
		return new modelExpression($this, 'IN', $value);
	}
	public function like($value){
		return new modelExpression($this, 'LIKE', $value);
	}
	public function setControl($controlClassName){
		$this->_control = new $controlClassName($this->_name);
		if ($this->_control->getProperty() === null){
			$this->_control->setProperty($this);
		}
	}
	public function setModel($model){
		$this->_model = $model;
	}
	/**
	 * @return model
	 */
	public function getModel(){
		return $this->_model;
	}
	public function getName(){
		return $this->_name;
	}
	public function getStorage(){
		return $this->getModel()->getStorage();
	}
	public function getControl(){
		return $this->_control;
	}
	public function getDefaultValue(){
		return $this->_calc($this->_defaultValue);
	}
	public function getInitialValue(){
		return $this->_calc($this->_initialValue);
	}
	public function getInternalValue($allowDefault = true){ // for sql SET
		return $this->_getInternalValue($allowDefault);
	}
	public function getValue($allowDefault = true){
		return $this->_getInternalValue($allowDefault);
	}
	protected function _calc($value){
		return is_object($value)?$value->getValue():$value;
	}
	protected function _getInternalValue($allowDefault = true){ // Template for both public and database variants
		if ($this->_value === null){
			if ($this->_initialValue === null){
				if ($allowDefault){
					return $this->_calc($this->_defaultValue);
				}
			}
			return $this->_calc($this->_initialValue);
		}
		return $this->_calc($this->_value);
	}
	public function setValue($value){
		$this->_value = $value;
	}
	public function setInitialValue($value){
		$this->_initialValue = $value;
	}
	public function isChangedValue(){
		return (($this->getValue() !== null) && ($this->getValue() != $this->getInitialValue()));
	}
	public function hasChangedValue(){
		return (($this->getValue() !== null) && ($this->getValue() != $this->getInitialValue()));
	}
	public function __toString(){
		return strval($this->getValue());
	}
	public function e(){
		return $this->getStorage()->quote($this->getValue());
	}
	public function html(){
		return htmlspecialchars($this->getValue());
	}
	// Storable value
	public function isValidValue($toSave = false){
		return true;
	}
	public function preSave(){
		if (!$this->isValidValue()){
			$this->setValue(null);
		}
	}
	public function preInsert(){}
	public function preUpdate(){}
	public function preDelete(){}
	public function postSave(){}
	public function postInsert(){}
	public function postUpdate(){}
	public function postDelete(){}
}