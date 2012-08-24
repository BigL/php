
<style type="text/css">
h3 {
    background-color: #CCCCCC;
    background-image: -moz-linear-gradient(center top , #FFFFFF 0%, rgba(255, 255, 255, 0) 100%);
    border-bottom: 1px solid #999999;
    padding: 4px;
    text-align: center;
}
.block{
  	background-color: #FFFFFF;
    background-image: -moz-linear-gradient(center top , #FFFFFF 0%, #EEEEEE 100%);
    border-bottom: 2px solid #000000;
    color: #222222;
    padding:20px 10px;
}
.address {
	border: medium none;
	list-style: none;font: 12px/16px 'Helvetica Neue',Arial,sans-serif;
}
</style>

{capture name=path}{l s='Credit/Debit Card' mod='paypaldirect'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h2>{l s='Order summary' mod='paypaldirect'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.'}</p>
{else}
<h3>{l s='Credit Card Payment' mod='paypaldirect'}</h3>
<form action="{$smarty.server.REQUEST_URI}" method="post" class="std">
	<br>
	{include file="$tpl_dir./errors.tpl"}
	<div>
		<br>
		{l s='Your card entered below will be charged:' mod='paypaldirect'}
        <br /><br />
        
		{if $currencies|@count > 1}
			{foreach from=$currencies item=currency}
				<span id="amount_{$currency.id_currency}" class="price" style="display:none;">{convertPriceWithCurrency price=$total currency=$currency}</span>
			{/foreach}
		{else}
			<span id="amount_{$currencies.0.id_currency}" class="price">{convertPriceWithCurrency price=$total currency=$currencies.0}</span>
		{/if}
		
	</div>
    <br />
	<p>	
	
	{if $currencies|@count > 1}
		{l s='We accept several currencies by credit or debit card.' mod='paypaldirect'}
		<br /><br />
		{l s='Please choose one of the following:' mod='paypaldirect'}
		<select id="payment_currency" name="payment_currency" onChange="showElemFromSelect('payment_currency', 'amount_')">
		{foreach from=$currencies item=currency}
			<option value="{$currency.id_currency}" {if $currency.id_currency == $cust_currency}selected="selected"{/if}>{$currency.iso_code}</option>
		{/foreach}
		</select>
		
		<script language="javascript">showElemFromSelect('payment_currency', 'amount_');</script>
	{else}
        <br />
		{l s='Note: For Credit and Debit cards we only accept payments in' mod='paypaldirect'}&nbsp;<b>{$currencies.0.iso_code}</b>
		<input type="hidden" name="payment_currency" value="{$currencies.0.id_currency}">
	{/if}
	</p>
    <br />
    <h3>Billing Name and Address</h3>
    {if $invoice->id}
	<div>
    <ul class="address block">
    	<li class="address_title">{l s='Note: Name must match Card Details below' mod='paypaldirect'}</li>
		{if $invoice->company}<li class="address_company">{$invoice->company|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_name">{$invoice->lastname|escape:'htmlall':'UTF-8'} {$invoice->firstname|escape:'htmlall':'UTF-8'}</li>
		<li class="address_address1">{$invoice->address1|escape:'htmlall':'UTF-8'}</li>
		{if $invoice->address2}<li class="address_address2">{$invoice->address2|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_city">{$invoice->postcode|escape:'htmlall':'UTF-8'} {$invoice->city|escape:'htmlall':'UTF-8'}</li>
		<li class="address_country">{$invoice->country|escape:'htmlall':'UTF-8'}</li>
        <li class="address_update"><a href="{$base_dir_ssl}address.php?id_address={$address.id_address|intval}" title="{l s='Update'}">{l s='Update'}</a></li>
	</ul>
    </div>
	{/if}
    <br />
	    
	<h3>{l s="Card Details" mod="creditcard"}</h3>
    <div class="paypaldirect_cardform block">
		<label for="creditCardType">Card Type:</label>
	    <select name="creditCardType" id="creditCardType">
	    	<option value="Visa" selected="selected">Visa</option>
	    	<option value="MasterCard">MasterCard</option>
	    	<option value="Discover">Discover</option>
	    	<option value="Amex">American Express</option>
	    </select>
	    <br /><br />
	    <label for="cardNumber">{l s='Credit Card Number:' mod='paypaldirect'}</label>
		<input type="text" name="cardNumber" id="cardNumber" value="{$cardNumber}" />
		<br /><br />
	    <label for="cardCVC">{l s='Card CVC Number:' mod='paypaldirect'}</label>
		<input type="text" name="cardCVC" id="cardCVC" value="{$cardCVC}" maxlength="4" size="4"/>
		<a href="{$this_path}img/CVC.jpg" target="_blank">Where is My CVC Number?</a>
		<br /><br />
	    <label>{l s='Expiration Date:' mod='paypaldirect'}</label>
	     {html_select_date prefix="expDate" time=$time start_year="-0" end_year="+15" display_days=false month_format="%m"}
	    <br /><br />
	    <p>{l s='Order placed from IP address: ' mod='paypaldirect'}{$ip_address}</p>
	    <div>
		<img src="{$this_path}pay_logos.gif" alt="{l s='credit card logos' mod='paypaldirect'}" style="margin: 0px 10px 5px 0px;" />
		</div>
	</div>
<p class="cart_navigation">
	<a href="{$base_dir_ssl}order.php?step=3" class="button_large">{l s='Other Payment Methods' mod='paypaldirect'}</a>
	<input type="submit" name="paymentSubmit" value="{l s='Continue' mod='paypaldirect'}&nbsp;&raquo;" class="exclusive_large" />
</p>
</form>
{/if}