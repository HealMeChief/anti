<?php 

namespace db;

class Prod {

     private $product_id;

    private $name;
    private $image_url;
    private $hover_image_url;

    private $price;

public function __construct($product_id, $name, $price, $image_url, $hover_image_url) {
    $this->product_id = $product_id;
    $this->name = $name;
    $this->price = $price;
    $this->image_url = $image_url;
    $this->hover_image_url = $hover_image_url;
}




public function get_product() {
    return 
    "<div class='item' id='$this->product_id'>".
        "<div class='m-img'>".
            "<img class='main-img' src='$this->image_url'>".
            "<img class='hover-img' src='$this->hover_image_url'>".
            "</div>".
            "<div class='m_text'>".
                "<p>$this->name</p>".
                "<p>$this->price &#8381;</p>".
            "</div>".
        "<div class='buy-button'>".
        "<button onclick=addToCart(this.closest('.item'))>В корзину</button>".
        "</div>".
    "</div>";
}


    
}