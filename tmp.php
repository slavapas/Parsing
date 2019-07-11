<?php 
    //connect DB credentials
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_NAME', 'parsing_db');

    
    
    // lib forparsing
    require_once('simple_html_dom.php');
    require_once('db.class.php');

    // URL for parsing
    $url = "http://www.eslceramics.co.uk/all-tiles/";
   // $url = "http://ananaska.com/category/category/novosti/";

   // DB connection
   $db=new DB(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    //    print_r($db->query('show tables'));
    //    exit;


    //define function to get elements from a page
    function getArticleData($url){
        $article = file_get_html($url);
        $h1 = $article -> find('h1',0)->innertext;  
        $content = $article -> find('.product_meta',0)->innertext;  
       // $data = compact(h1,content);
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
        global $db;
        
        //выводим номер страниц где мы находимся
        echo PHP_EOL.$url.PHP_EOL.PHP_EOL;

       // get page
        $html = file_get_html($url);
       
        //get each article link
        foreach ( $html->find ('a.woocommerce-LoopProduct-link') as $link_to_product){
            //each articles link add to DB
            $atricles_url=$db->escape($link_to_product->href);
            //add to DB the url of pages from site
            $sql="
                insert into atricles
                set url='{$atricles_url}'
            ";
            $db->query($sql);

            echo $link_to_product->href . PHP_EOL;
            
                // test how the function getArticleData() works
            //print_r(getArticleData($link_to_product->href));
        }

         //apply RECURSIA = вызов функции в самой функции
        if ( $next_link = $html->find('a.next', 0)){
            getProducts($next_link->href);
        }
    }
   
    //call the function getProducts
   getProducts($url);










?>