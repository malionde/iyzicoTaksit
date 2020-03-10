<?php
//Sandbox options 
require_once('config.php');


# create request class
$request = new \Iyzipay\Request\RetrieveInstallmentInfoRequest();
$request->setLocale(\Iyzipay\Model\Locale::TR);
$request->setConversationId(uniqid());
//$request->setBinNumber("554960");
$request->setPrice("100");

# make request
$installmentInfo = \Iyzipay\Model\InstallmentInfo::retrieve($request, Config::options());

$result = $installmentInfo->getRawResult();
$result = json_decode($result);

$data['statusApi'] = $installmentInfo->getStatus();

if($data['statusApi'] != 'success')
    exit('Error');
    
$result = $result->installmentDetails;
$data['result'] = $result;

$data['installments'] = array();
$data['banks'] 	= array();
$data['prices'] = array();

foreach ($result as $key => $dataParser) {

    $data['banks'][$key] = $dataParser->cardFamilyName;
    
    foreach ($dataParser->installmentPrices as $key => $installment) {
    
        $data['installments'][$key] = $installment->installmentNumber;
    }
}

// The new tab content
echo '<h2>Taksit Seçenekleri</h2>';
echo '<p>Taksit tablosu burada yer alacak.</p>';
echo 'Ürünün Fiyatı '.$regular_price.' TL dir.';
echo "<br><br>";

print_r($installmentInfo);
?>

{% if status %}
<div class="mainDiv">
<div class="topDiv">
		<div class="headBoxInstallment">
			<p>{{installment}}</p>
		</div>
		{%for bank in banks  %}
			<div class="headBox">
				<p>{{bank}}</p>
			</div>
		{% endfor %}
</div>
<div class="centerDiv">
			<div class="installmentCount">
		{% for installment in installments  %}
				{% if installment == '1' %}
					<p>{{prepay}}</p>
				{% else %}
					<p >{{installment}}</p>
				{% endif %}
		{% endfor %}
			</div>


		{% for i in result %}
			<div class="centerBox">
				{% for j in i.installmentPrices %}
					{% if j.installmentNumber == '1' %}
						<p  class="prepay {{i.cardFamilyName}}Avantage">{{j.totalPrice}} {{symbolRight}}</p>
					{% else %}
						<p class="{{i.cardFamilyName}}"><strong>{{j.installmentPrice}} {{symbolRight}} x {{j.installmentNumber}}</strong>
						<br>
						TOPLAM: {{j.totalPrice}} {{symbolRight}}</p>
					{% endif %}
				{% endfor %}    
			</div>
		{% endfor %}

</div>
</div>
<p style="float:right;margin:5px;">Taksit bilgileri gösterim amaçlıdır.</p>
{% endif %}

