<?php //eval


    function display_php_result(){

        $url = "http://graph.facebook.com/facebook";
        $json = file_get_contents($url);
        $response = json_decode($json);

        $html = "";
        $html.= title($response->name);
        $html.= text($response->about);
        $html.= aref($response->link);

        return $html;
    }

    function title($str){
        return "<h1> $str </h1>";
    }

    function text($str){
        return "<p> $str </p>";
    }

    function aref($str){
        return "<a href='$str'>$str</a>";
    }

//file must return for this to work
return display_php_result();
?>