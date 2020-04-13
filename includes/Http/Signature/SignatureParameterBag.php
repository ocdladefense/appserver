<?php


class SignatureParameterBag {

		// For multi-parameter bags,
		// specify the separator that is to be
		// used to implode the various parameters
		//  into a single string value.
		const PARAMETER_SEPARATOR = ", ";



		// Reference to the underlying parameters in this bag.
    private $params = array();



    public function __construct() {
    		$this->params = func_get_args();
    }


		public function addParameter(Parameter $param) {
			$this->params[] = $param;
		}

		/**
		 * Return the parameters as an instance
		 *  of HttpHeader.  The header
		 *  should be assigned the given name.
		 */
    public function getAsHttpHeader($name){
        return new HttpHeader($name,implode(self::PARAMETER_SEPARATOR,$this->params));
    }
    

}