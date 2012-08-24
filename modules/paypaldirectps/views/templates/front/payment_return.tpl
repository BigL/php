{if $status == 'ok'}
	<p>{l s='Your dredit card order from' mod='paypaldirect'} <span class="bold">{$shop_name}</span> {l s='has been processed.' mod='paypaldirect'}
		<br /><br />
        {l s='Your order reference number is: ' mod='paypaldirect'}{$transactionID}
        <br /><br />
        {l s='For any questions or for further information, please contact our' mod='paypaldirect'} <a href="{$base_dir_ssl}contact-form.php">{l s='customer support' mod='paypaldirect'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We encountered a problem processing your order. If you think this is an error, you can contact our' mod='paypaldirect'} 
		<a href="{$base_dir_ssl}contact-form.php">{l s='customer support depertment who will be pleased to assist you.' mod='paypaldirect'}</a>.
	</p>
{/if}
