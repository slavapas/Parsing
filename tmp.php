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
       global $db;

        $article = file_get_html($url);

        $h1=$db->escape($article->find('h1', 0)->innertext);
        $content=$db->escape($article->find('.product_meta', 0)->innertext);

        //$data = compact(h1,content);
         $data = array(
             'h1'       => $h1,
             'content'  => $content
         );

        $sql="
            update articles
            set h1='{$h1}',
                content='{$content}',
                date_parsed=NOW()
            where url='{$url}'
        ";
        $db->query($sql);

        return $data;

        //$h1 = $article -> find('h1',0)->innertext;  
        //$content = $article -> find('.product_meta',0)->innertext;  
       // $data = compact(h1,content);
        // $data = array(
        //     'h1' => $h1,
        //     'content' => $content
        // ); 
        //return $data;
    }

    // get the content from $html without html tags
    //print_r($html->plaintext);

    // get the content from $html with html tags
    //print_r($html->innertext);

    // get the specific element from $html without according to its html tags
   //foreach ( $html->find ('a.read-more-link') as $link_to_articles){
    function getArticles($url){
        global $db;
        
        //выводим номер страниц где мы находимся
        echo PHP_EOL.$url.PHP_EOL.PHP_EOL;

       // get page
        $html = file_get_html($url);
       
        //get each article link
        foreach ( $html->find ('a.woocommerce-LoopProduct-link') as $link_to_articles){
            //each articles link add to DB
            $articles_url=$db->escape($link_to_articles->href);
            //add to DB the url of pages from site
            $sql="
                insert ignore into articles
                set url='{$articles_url}'
            ";
            $db->query($sql);
            
            //parse and save the current article by current link
            getArticleData($link_to_articles->href);

            echo $link_to_articles->href . PHP_EOL;
            
                // test how the function getArticleData() works
            //print_r(getArticleData($link_to_articles->href));
        }

         //apply RECURSIA = вызов функции в самой функции
        if ( $next_link = $html->find('a.next', 0)){
            getArticles($next_link->href);
        }
    }
   
    //call the function getArticles
   getArticles($url);
   










?>