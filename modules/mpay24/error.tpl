<!-- 
     @author            support@mpay24.com
     @filesource        error.tpl
     @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
-->
{capture name=path}<a href="{$link->getPageLink('order.php', true)}">{l s='Your shopping cart' mod='mpay24'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='mPAY24' mod='mpay24'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{$status}</h2>
	<div class="error">
			<h3>{$returnCode}</h3>
			<p>{$externalStatus}</p>
		<p><a href="{$base_dir}" class="button_small" title="{l s='Back' mod='mpay24'}">&laquo; {l s='Back' mod='mpay24'}</a></p>
	</div>
