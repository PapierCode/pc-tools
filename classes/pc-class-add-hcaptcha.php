<?php

/**
*
* * Validation reCaptcha
*
*
**/

class PC_Hcaptcha {

    private $api_secret;
    private $api_site;


    /*====================================
    =            Constructeur            =
    ====================================*/

    function __construct( $api_site, $api_secret ) {

        $this->api_secret = $api_secret;
        $this->api_site = $api_site;

    }


    /*=====  FIN Constructeur  ======*/

    /*=================================
    =            Affichage            =
    =================================*/

    public function display() {

		echo '<script src="https://www.hCaptcha.com/1/api.js" async defer></script>';
        echo '<div class="h-captcha" data-sitekey="'.$this->api_site.'"></div>';

    }


    /*=====  FIN Affichage  ======*/

    /*==================================
    =            Validation            =
    ==================================*/

    public function validate( $hcaptcha_response ) {

        if ( empty( $hcaptcha_response ) ) { return false; }

        $query_args = array(
            'secret'    => $this->api_secret,
            'response'  => $hcaptcha_response
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
