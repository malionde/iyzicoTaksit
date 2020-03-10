<style>
html {
  box-sizing: border-box;
}
html * {
  box-sizing: inherit;
}

body {
  padding: 30px;
  font-family: 'Arial Narrow', Arial, sans-serif;
  font-size: 14px;
  line-height: 1.4;
}

.cards {
  margin-left: 6px;
}
.cards:after {
  content: "";
  display: table;
  clear: both;
}

.card {
  width: 25%;
  margin-bottom: 30px;
  border-top: 1px solid #b4b4b4;
  float: left;
}
.card:nth-child(7n) {
  border-right: 1px solid #b4b4b4;
}
.card__head {
  height: 60px;
  border-left: 1px solid #b4b4b4;
  border-bottom: 2px solid #45d7e6;
  text-align: center;
  font-weight: bold;
  line-height: 60px;
  letter-spacing: 2px;
}
.card__content {
  position: relative;
}
.card__content:after {
  content: "";
  display: table;
  clear: both;
}
.card__col {
  float: left;
  width: 50%;
}
.card__col--installment {
  position: absolute;
  right: 100%;
  display: none;
  width: 66px;
}
.card__col--installment:before {
  position: absolute;
  top: -2px;
  left: 0;
  right: 0;
  content: "";
  display: block;
  height: 2px;
  background-color: #45d7e6;
}
.card:nth-child(0n + 1) .card__col--installment {
  display: block;
}
.card__cell {
  height: 60px;
  line-height: 60px;
  text-align: center;
  border-left: 1px solid rgba(106, 106, 106, 0.5);
  border-bottom: 1px solid rgba(106, 106, 106, 0.5);
  white-space: nowrap;
}
.card__cell--head {
  height: 40px;
  line-height: 40px;
  background-color: #d6d6d6;
}
.card--bonus .card__head {
  color: #009a4c;
}
.card--bonus .card__col--default {
  background-color: rgba(0, 154, 76, 0.25);
}
.card--world .card__head {
  color: #860092;
}
.card--world .card__col--default {
  background-color: rgba(134, 0, 146, 0.25);
}
.card--maximum .card__head {
  color: #ff0097;
}
.card--maximum .card__col--default {
  background-color: rgba(255, 0, 151, 0.25);
}
.card--axess .card__head {
  color: #ffcc00;
}
.card--axess .card__col--default {
  background-color: rgba(255, 204, 0, 0.25);
}
.card--cardfinans .card__head {
  color: #0060b4;
}
.card--cardfinans .card__col--default {
  background-color: rgba(0, 96, 180, 0.25);
}
.card--paraf .card__head {
  color: #00b4ff;
}
.card--paraf .card__col--default {
  background-color: rgba(0, 180, 255, 0.25);
}
.card--BankkartCombo .card__head {
  color: #FF0000;
}
.card--BankkartCombo .card__col--default {
  background-color: rgba(255, 0, 0, 0.25);
}
@media screen and (max-width: 991px) {
  .card {
    width: 33.3333333333%;
  }
  .card .card__col--installment {
    display: none;
  }
  .card:nth-child(3n + 1) .card__col--installment {
    display: block;
  }
  .card:nth-child(3n) {
    border-right: 1px solid #b4b4b4;
  }
}
@media screen and (max-width: 767px) {
  .card {
    width: 50%;
  }
  .card:nth-child(n + 1) .card__col--installment {
    display: none;
  }
  .card:nth-child(2n + 1) .card__col--installment {
    display: block;
  }
  .card:nth-child(n + 1) {
    border-right: none;
  }
  .card:nth-child(2n) {
    border-right: 1px solid #b4b4b4;
  }
}

@media screen and (max-width: 479px) {
  .cards {
    margin-left: 50px;
  }

  .card {
    width: 100%;
  }
  .card__col--installment {
    width: 50px;
  }
  .card:nth-child(n + 1) .card__col--installment {
    display: block;
  }
  .card:nth-child(n + 1) {
    border-right: 1px solid #b4b4b4;
  }
}
</style>
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

    echo ('<div class="cards">');
    # data parsing 
    foreach ($result as $key => $dataParser) {
    
        $data['banks'][$key] = $dataParser->cardFamilyName;
        $div = '<div class="card card--';
        $div2 = '">';
        echo ($div . $data['banks'][$key] . $div2); 

        $divIn1 = '<div class="card__head">';
        $divIn2 = '</div>';
        $divIn3 = '<div class="card__content">';
        echo($divIn1 . $data['banks'][$key] . $divIn2 . $divIn3);

        $div1 = '<div class="card__cell card__cell--value">';
        $div2 = '</div>';

        echo('<div class="card__col card__col--installment"><div class="card__cell card__cell--head">Taksit</div>');    
        foreach ($dataParser->installmentPrices as $key => $installment) {
            $data['installments'][$key] = $installment->installmentNumber;
            echo ($div1 . $data['installments'][$key] . $div2);
        }
        echo('</div>');
        
        echo('<div class="card__col card__col--default"><div class="card__cell card__cell--head">Tutar</div>');
        foreach ($dataParser->installmentPrices as $key => $installment) {
        
            $data['installmentPrice'][$key] = $installment->installmentPrice;
            echo ($div1 . $data['installmentPrice'][$key] . $div2);

        }
        echo('</div>');

        echo('<div class="card__col card__col--default"><div class="card__cell card__cell--head">Toplam</div>');
        foreach ($dataParser->installmentPrices as $key => $installment) {
        
            $data['totalPrices'][$key] = $installment->totalPrice;
            echo ($div1 . $data['totalPrices'][$key] . $div2);

        }
        echo('</div>');
    echo ('</div></div>');
    }
echo('</div>');
}

?>
