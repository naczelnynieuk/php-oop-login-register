<?php

namespace MyApp;

/**
 * Klasa odpowiadajaca za token w formularzach
 */
class Token {

/**
 * Generuje token i zapisuje go w sesji
 * @return string
 */
	public static function generate() {
		return Session::put(Config::get('session/token_name'), md5(uniqid()));
    }

/**
 * Sprawdza czy token jest poprawny
 * @param  string $token Token pobrany z value formularza
 * @return boolean
 */
    public static function check($token) {
        $tokenName = Config::get('session/token_name');

        if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
            Session::delete($tokenName);
            return true;
        }
        return false;
    }
}