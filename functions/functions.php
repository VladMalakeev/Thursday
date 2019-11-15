<?php

function parse_put(){
    global $_PUT;

    /* PUT data comes in on the stdin stream */
    $putdata = fopen("php://input", "r");

    /* Open a file for writing */
    // $fp = fopen("myputfile.ext", "w");

    $raw_data = '';

    /* Read the data 1 KB at a time
       and write to the file */
    while ($chunk = fread($putdata, 1024))
        $raw_data .= $chunk;

    /* Close the streams */
    fclose($putdata);

    // Fetch content and determine boundary
    $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

    if(empty($boundary)){
        parse_str($raw_data,$data);
        $GLOBALS[ '_PUT' ] = $data;
        return;
    }

    // Fetch each part
    $parts = array_slice(explode($boundary, $raw_data), 1);
    $data = array();

    foreach ($parts as $part) {
        // If this is the last part, break
        if ($part == "--\r\n") break;

        // Separate content from headers
        $part = ltrim($part, "\r\n");
        list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

        // Parse the headers list
        $raw_headers = explode("\r\n", $raw_headers);
        $headers = array();
        foreach ($raw_headers as $header) {
            list($name, $value) = explode(':', $header);
            $headers[strtolower($name)] = ltrim($value, ' ');
        }

        // Parse the Content-Disposition to get the field name, etc.
        if (isset($headers['content-disposition'])) {
            $filename = null;
            $tmp_name = null;
            preg_match(
                '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                $headers['content-disposition'],
                $matches
            );
            list(, $type, $name) = $matches;

            //Parse File
            if( isset($matches[4]) )
            {
                //if labeled the same as previous, skip
                if( isset( $_FILES[ $matches[ 2 ] ] ) )
                {
                    continue;
                }

                //get filename
                $filename = $matches[4];

                //get tmp name
                $filename_parts = pathinfo( $filename );
                $tmp_name = tempnam( ini_get('upload_tmp_dir'), $filename_parts['filename']);

                //populate $_FILES with information, size may be off in multibyte situation
                $value = substr($value,1,strlen($value));
                $_FILES[ $matches[ 2 ] ] = array(
                    'error'=>0,
                    'name'=>$filename,
                    'tmp_name'=>$tmp_name,
                    'size'=>strlen( $body ),
                    'type'=>$value
                );

                //place in temporary directory
                file_put_contents($tmp_name, $body);
            }
            //Parse Field
            else
            {
                $data[$name] = substr($body, 0, strlen($body) - 2);
            }
        }

    }
    $GLOBALS[ '_PUT' ] = $data;
    return $_PUT;
}

function getPutDeleteData(){
    switch ($_SERVER['CONTENT_TYPE']){
        case 'application/json':
             return json_decode(file_get_contents("php://input"),true);
        case 'application/x-www-form-urlencoded':
            parse_str(file_get_contents("php://input"),$arr);
            return $arr;
        default:
              if(preg_match('/(multipart\/form-data)/',$_SERVER['CONTENT_TYPE'])){
                  return parse_put();
              }
              return false;

    }
}

function uploadImage($path){
    $types = array('image/gif','image/png','image/jpeg');
    $size = 5242880;

    if(!in_array($_FILES['image']['type'],$types)){
        throw new Exception('Valid formats: gif,png,jpg.');
    }

    if($_FILES['image']['size']>$size){
        throw new Exception('Max size 5mb.');
    }
    $type = preg_split('/\//',$_FILES['image']['type'])[1];
    $name = md5(uniqid(rand(), true));
    if(!@copy($_FILES['image']['tmp_name'],$path.$name.'.'.$type)){
        throw new Exception('Image not saved.');
    }else return $name.'.'.$type;
}