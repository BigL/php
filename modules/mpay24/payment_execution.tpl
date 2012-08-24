<!-- 
     @author            support@mpay24.com
     @filesource        payment_execution.tpl
     @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
-->
{capture name=path}<a href="{$link->getPageLink('order.php', true)}">{l s='Your shopping cart' mod='mpay24'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='mPAY24' mod='mpay24'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summary' mod='mpay24'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}
<form action="{$this_path_ssl}submit.php" method="post">
<p>
    <img src="images/mpay24_logo.gif" alt="{l s='mPAY24' mod='mpay24'}" style="margin-bottom: 5px" />
    <br />{l s='You have chosen to pay with mPAY24.' mod='mpay24'}
    <br /><br />{l s='Your payment will be processed securely through the award winning and PCI certified payment service provider mPAY24 GmbH.' mod='mpay24'}
    <br/><br />
    {l s='Here is a short summary of your order:' mod='mpay24'}
</p>
<p style="margin-top:20px;">
    - {l s='The total amount of your order is' mod='mpay24'}
        <span class="price">{convertPriceWithCurrency price=$total currency=$currency}</span> {if $use_taxes == 1}{l s='(tax incl.)' mod='mpay24'}{/if}
</p>
<p>
    <b>{l s='Please confirm your order by clicking \'I confirm my order\'' mod='mpay24'}.</b>
</p>
<p class="cart_navigation">
    <a href="{$link->getPageLink('order.php', true)}?step=3" class="button_large">{l s='Other payment methods' mod='mpay24'}</a>
    <input type="submit" name="submitPayment" value="{l s='I confirm my order' mod='mpay24'}" class="exclusive_large" />
</p>
</form>