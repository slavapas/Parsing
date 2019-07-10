<?php 
    // lib forparsing
    require_once('simple_html_dom.php');

    // URL for parsing
    $url = "http://www.eslceramics.co.uk/all-tiles/";
   // $url = "http://ananaska.com/category/category/novosti/";

    //define function to get elements from a page
    function getArticleData($url){
        $article = file_get_html($url);
        $h1 = $article -> find('h1',0)->innertext;  
        $content = $article -> find('.product_meta',0)->innertext;  
        $data = array(
            'h1' => $h1,
            'content' => $content
        ); 
        return $data;
    }

    // get the content from $html without html tags
    //print_r($html->plaintext);

    // get the content from $html with html tags
    //print_r($html->innertext);

    // get the specific element from $html without according to its html tags
   //foreach ( $html->find ('a.read-more-link') as $link_to_product){
    function getProducts($url){
        //выводим номер страниц где мы находимся
        echo PHP_EOL.$url.PHP_EOL.PHP_EOL;

       // get page
        $html = file_get_html($url);
       
        //get each article link
        foreach ( $html->find ('a.woocommerce-LoopProduct-link') as $link_to_product){
            echo $link_to_product->href . PHP_EOL;
            print_r(getArticleData($link_to_product->href));
        }

         //apply RECURSIA = вызов функции в самой функции
        if ( $next_link = $html->find('a.next', 0)){
            getProducts($next_link->href);
        }
    }
   
    //call the function getProducts
   getProducts($url);










?>