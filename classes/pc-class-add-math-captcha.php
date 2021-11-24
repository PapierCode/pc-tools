<?php

/**
*
* Math Captcha
*
**/

class PC_MathCaptcha {

	public $math;
	public $msg_error;

	private $lang; 
	
	private $cipher_method;
	private $pass_phrase;
	private $iv;


    /*====================================
    =            Constructeur            =
    ====================================*/

    function __construct( $pass_phrase, $iv, $lang ) {

		switch ( $lang ) {
			case 'fr':
				$msg_error = '<strong>Résolvez le nouveau calcul</strong> (protection contre les spams)';
				break;
			case 'en':
				$msg_error = '<strong>Solve the new calculation</strong> (spam protection)';
				break;
		}
		$this->msg_error = apply_filters( 'pc_filter_mathcaptcha_msg_error', $msg_error, $lang );
		$this->lang = $lang;
		
		$types = array( 'add', 'sub' );
		$this->math = array( rand( 1, 10 ), rand( 1, 10 ), $types[ rand( 0, 1 ) ] );
		
		$this->cipher_method = 'AES-128-CTR';
		$this->pass_phrase = $pass_phrase;
		$this->iv = $iv;

    }


    /*=====  FIN Constructeur  ======*/

	/*=============================
	=            Champ            =
	=============================*/
	
	public function get_field_label_text() {

		$math = $this->math;

		switch ( $this->lang ) {
			case 'fr':
				$operator = ( 'add' == $math[2] ) ? 'plus' : 'moins';
				$label_text = 'Combien font '.$math[0].'&nbsp;'.$operator.'&nbsp;'.$math[1].'&nbsp;?';
				break;
			case 'en':
				$operator = ( 'add' == $math[2] ) ? 'plus' : 'minus';
				$label_text = 'How many do '.$math[0].'&nbsp;'.$operator.'&nbsp;'.$math[1].'&nbsp;?';
				break;
		}

		return apply_filters( 'pc_filter_mathcaptcha_label_text', $label_text , $math, $operator, $this->lang );

	}
	
	public function get_field_inputs() {

		return '<input type="number" id="form-captcha" name="form-captcha" value="" required /><input type="hidden" name="captcha-math" value="'.$this->get_encode_math().'" />';

	}
	
	
	/*=====  FIN Champ  =====*/

	/*===========================================
	=            Encodage / Décodage            =
	===========================================*/
	
	public function get_encode_math() {
		
		$encryption = openssl_encrypt(
			implode( '/', $this->math ),
			$this->cipher_method,
			$this->pass_phrase,
			0,
			$this->iv
		);
		
		return $encryption;

	}
	
	public function get_decode_math( $encryption ) {

		$decryption = openssl_decrypt (
			$encryption,
			$this->cipher_method,
			$this->pass_phrase,
			0,
			$this->iv
		);

		return $decryption;

	}
	
	
	/*=====  FIN Encodage / Décodage  =====*/

    /*==================================
    =            Validation            =
    ==================================*/

    public function validate() {

		$return = false;
		
		if ( isset( $_POST['captcha-math'] )) {

			$math = $this->get_decode_math( $_POST['captcha-math'] );
			$math = explode( '/', $math );

			if ( 'add' == $math[2] ) {
				$user_result = (int) $math[0] + (int) $math[1];
			} else {
				$user_result = (int) $math[0] - (int) $math[1];
			}

			if ( $user_result == (int) $_POST['form-captcha'] ) { $return = true; }

		}

		return $return;

    }

    /*=====  FIN Validation  ======*/

}
