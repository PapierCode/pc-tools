<?php

/**
*
* * Validation reCaptcha
*
*
**/

class PC_Hcaptcha {

	public $msg_error;

    private $api_secret;
    private $api_site;


    /*====================================
    =            Constructeur            =
    ====================================*/

    function __construct( $api_site, $api_secret ) {

		$this->msg_error = apply_filters( 'pc_filter_hcaptcha_msg_error', 'Cochez la case <strong>Je suis un humain</strong>, et si nécessaire suivez les instructions' );

        $this->api_secret = $api_secret;
        $this->api_site = $api_site;

    }


    /*=====  FIN Constructeur  ======*/

    /*=================================
    =            Affichage            =
    =================================*/

	public function get_field_label_text() {

		return apply_filters( 'pc_filter_hcaptcha_label_text', 'Protection contre les spams' );

	}

    public function display() {

		echo '<script src="https://js.hCaptcha.com/1/api.js" async defer></script>';
        echo '<div class="h-captcha" data-sitekey="'.$this->api_site.'"></div>';

    }


    /*=====  FIN Affichage  ======*/

    /*==================================
    =            Validation            =
    ==================================*/

    public function validate() {

        if ( empty( $_POST['h-captcha-response'] ) ) { return false; }

        $query_args = array(
            'secret'    => $this->api_secret,
            'response'  => $_POST['h-captcha-response']
		);

		$verify = curl_init();

		curl_setopt( $verify, CURLOPT_URL, 'https://hcaptcha.com/siteverify' );
		curl_setopt( $verify, CURLOPT_POST, true);
		curl_setopt( $verify, CURLOPT_POSTFIELDS, http_build_query( $query_args ) );
		curl_setopt( $verify, CURLOPT_RETURNTRANSFER, true );

		$response = json_decode( curl_exec( $verify ) );

		return ( $response->success ) ? true : false;

    }

    /*=====  FIN Validation  ======*/

}
