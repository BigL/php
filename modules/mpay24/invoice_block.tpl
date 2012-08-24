<!-- 
     @author            support@mpay24.com
     @filesource        invoice_block.tpl
     @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
-->
<fieldset style="width: 400px;">

<legend>

     {l s='MPAY24 transaction information' mod='mpay24'}

</legend>

<div id="info" style="border: solid red 1px;">
<table>
<tr><td>MPAYTID:</td> <td>{$mpaytid}</td></tr>
<tr><td>{l s='Payment system' mod='mpay24'}:</td> <td>{$p_type}</td></tr>
<tr><td>Brand:</td> <td>{$brand}</td></tr>
<tr><td>{l s='Approval code' mod='mpay24'}:</td> <td>{$appr_code}</td></tr>
</table>
</div>

</fieldset>