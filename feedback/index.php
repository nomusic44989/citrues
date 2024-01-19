<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обратная связь");
?><div class="contact-form pt-90 pb-30">
	<div class="container">
		<div class="row">
			<div class="section-heading text-center mb-70">
				<h2>Форма Обратной связи</h2>
				<p>
					 Оставьте заявку в форме ниже и мы свяжемся с вами!
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="contact-info">
				</div>
			</div>
		</div>
	</div>
</div>
 <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback", 
	"bootstrap_v4", 
	array(
		"EMAIL_TO" => "nomusic44989@gmail.com",
		"EVENT_MESSAGE_ID" => array(
		),
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"REQUIRED_FIELDS" => array(
			0 => "NAME",
			1 => "EMAIL",
		),
		"USE_CAPTCHA" => "N",
		"COMPONENT_TEMPLATE" => "bootstrap_v4"
	),
	false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>