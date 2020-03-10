<?php
/*
Plugin Name: iyzico Taksit Seçenekleri
Plugin URI: malio.me
Description: Kredi kartı için taksit seçeneklerini ürün sayfası içerisinde yeni bir tabda gösterir. 
Version: 1.0.0
Author: Mehmet Ali Önde
Author URI: malio.me
License: GNU
*/

add_filter('woocommerce_product_tabs', 'woo_new_product_tab');
function woo_new_product_tab($tabs)
{
    // Adds the new tab
    $tabs['test_tab'] = array(
        'title' => __('Taksit Seçenekleri', 'woocommerce'),
        'priority' => 50,
        'callback' => 'woo_new_product_tab_content'
    );
    return $tabs;
}

function woo_new_product_tab_content()
{
    // The new tab content
    echo '<h2>Taksit Seçenekleri</h2>';
    echo '<p>Taksit tablosu burada yer alacak.</p>';
    echo 'Ürünün Fiyatı '.$regular_price.' TL dir.';
    echo "<br><br>";
    //Sandbox options 
    require_once('config.php');
    
    //Get product price
    global $product;
    $regular_price = esc_attr( $product->get_display_price() );

    # create request class
    $request = new \Iyzipay\Request\RetrieveInstallmentInfoRequest();
    $request->setLocale(\Iyzipay\Model\Locale::TR);
    $request->setConversationId(uniqid());
    //$request->setBinNumber("554960");
    $request->setPrice("$regular_price");
    
    # make request
    $installmentInfo = \Iyzipay\Model\InstallmentInfo::retrieve($request, Config::options());
    
    # json decode
    $result = $installmentInfo->getRawResult();
    $result = json_decode($result);
    
    $data['statusApi'] = $installmentInfo->getStatus();
    
    if($data['statusApi'] != 'success')
        exit('Error');
        
    $result = $result->installmentDetails;
    $data['result'] = $result;
    
    # declaration 
    $data['installments'] = array();
    $data['banks'] 	= array();
    $data['totalPrices'] = array();
    $data['installmentPrice'] = array();

    # data parsing 
    foreach ($result as $key => $dataParser) {
    
        $data['banks'][$key] = $dataParser->cardFamilyName;
        print_r($data['banks'][$key]);
        echo('<br>');

        foreach ($dataParser->installmentPrices as $key => $installment) {
        
            $data['installmentPrice'][$key] = $installment->installmentPrice;
            $data['totalPrices'][$key] = $installment->totalPrice;
            $data['installments'][$key] = $installment->installmentNumber;
            echo "installmentPrice :";
            print_r($data['installmentPrice'][$key]);
            echo "  ";
            echo "totalPrice :";
            print_r($data['totalPrices'][$key]);
            echo "  ";
            echo "installmentNumber :";
            print_r($data['installments'][$key]);
            echo('<br>');
        }
    }
}

?>
