<?php

/*
    @author  Pablo Bozzolo boctulus@gmail.com
*/

namespace boctulus\BzzCSVImport\libs;

use boctulus\BzzCSVImport\libs\Strings;

class Url 
{    
    static function currentUrl(){
        if (is_cli()){
            return '';
        }

        $actual_link = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $actual_link;
    }

    // Body decode
    static function bodyDecode(string $data){
        //throw new \Exception("aaa");

        $headers  = apache_request_headers();
        $content_type = $headers['Content-Type'] ?? $headers['content-type'] ?? null;

        #var_dump('content type: '. $content_type);

        if (!empty($content_type)){
            // Podría ser un switch-case aceptando otros MIMEs

            if (Strings::contains('application/x-www-form-urlencoded', $content_type)){
                #var_dump('deco encoded');

                $data = urldecode($data);
                $data = Url::parseStrQuery($data);
            } else {
                #var_dump('deco json');

                $data = json_decode($data, true);

                if ($data === null) {
                    throw new \Exception("JSON inválido");
                }
            }

        }

        return $data;
    }

    static function has_ssl( $domain ) {
        $ssl_check = @fsockopen( 'ssl://' . $domain, 443, $errno, $errstr, 30 );
        $res = !! $ssl_check;
        
        if ( $ssl_check ) {
             fclose( $ssl_check ); 
        }
        
        return $res;
    }

    static function http_protocol(){ 
        $protocol = self::has_ssl($_SERVER['HTTP_HOST']) ? 'https' : 'http';
        
        return $protocol;
    }

    /*
		Patch for parse_str() native function

		It could be more efficient and precise if I use a preg_replace_callback and
		take note about which parameter was substituted
	*/
	static function parseStrQuery(string $s){
		$rep = '__DOT__';

		$s = str_replace('.', $rep, $s);

		parse_str($s, $result);
		
		foreach ($result as $k => $v){
			$pos = strpos($k, $rep);

			if ($pos !== false){
				$k2 =  str_replace($rep, '.', $k);
				$result[$k2] = $v;
				unset($result[$k]);
			} else {
                // parche 2022
                $result[$k] = str_replace($rep, '.', $result[$k]);
            }
		}

		return $result;
	}	

	static function queryString(){
		return static::parseStrQuery($_SERVER['QUERY_STRING']);		
	}	

    static function getQueryParam(string $url, string $param){
        $query = parse_url($url, PHP_URL_QUERY);

        $x = null;
        if ($query != null){
            $q = explode('&', $query); 
            foreach($q as $p){
                if (Strings::startsWith($param . '=', $p)){
                    $_x = explode('=', $p);
                    $x  = $_x[count($_x)-1];                    
                }
            }
        }

        return $x;
    }

    static function getBaseUrl($url, bool $include_path = false)
    {
        $url_info = parse_url($url);
        return  $url_info['scheme'] . '://' . $url_info['host'];
    }

    static function consume_api(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null, $decode = true)
    {  
        if ($headers === null){
            $headers = [];
        } else {
            if (!Arrays::is_assoc($headers)){
                $_hs = [];
                foreach ($headers as $h){                   
                    list ($k, $v)= explode(':', $h, 2);                    
                    $_hs[$k] = $v;
                }

                $headers = $_hs;
            }
        }

        if ($options === null){
            $options = [];
        }

        if ($options === null){
            $options = [];
        }

        $keys = array_keys($headers);

        $content_type_found = false;
        foreach ($keys as $key){
            if (strtolower($key) == 'content-type'){
                $content_type_found = $key;
                break;
            }
        }

        $accept_found = false;
        foreach ($keys as $key){
            if (strtolower($key) == 'accept'){
                $accept_found = $key;
                break;
            }
        }

        if (!$content_type_found){
            $headers = array_merge(
                [
                    'Content-Type' => 'application/json'
                ], 
                ($headers ?? [])
            );
        } 
        
        
        if ($accept_found) { 
            if (Strings::startsWith('text/plain', $headers[$accept_found]) || 
                Strings::startsWith('text/html', $headers[$accept_found])){
                $decode = false;
            }
        }
   
        if (/* $headers[$content_type_found] == 'application/json' || */ is_array($body)){
            $data = json_encode($body);
        } else {
            $data = $body;
        }

        $curl = curl_init();

        $http_verb = strtoupper($http_verb); 
    
        if ($http_verb != 'GET' && !empty($data)){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $headers['Content-Length']   = strlen($data);
        }
    
        $h = [];
        foreach ($headers as $key => $header){
            $h[] = "$key: $header";
        }

        $options = [
            CURLOPT_HTTPHEADER => $h
        ] + ($options ?? []);

        //Files::dump($headers, 'HEADERS.txt');

        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '' );
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0 );
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_verb);
   
        // https://stackoverflow.com/a/6364044/980631
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_HTTP200ALIASES, (array)400);    


        $response  = curl_exec($curl);
        $err_msg   = curl_error($curl);	
        $http_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        curl_close($curl);
    

        $data = $decode ? json_decode($response, true) : $response;
    
        $ret = [
            'data'      => $data,
            'http_code' => $http_code,
            'error'     => $err_msg
        ];
    
        return $ret;
    }    
    
}

