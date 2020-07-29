{* HEADER *}


{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}
<div>
  <h1 id="page-title" class="title"> LibreOffice Calc Mail Merge </h1>
</div>
<div class="messages status no-popup">{include file="CRM/Contact/Form/Task.tpl"}</div>
{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">
      {$form.$elementName.html}
      {if $elementName eq 'merge_letter_for_same_address'}
        <span>{help id="id-merge-letter-help"}</span>
      {/if}
    </div>
    <div class="clear"></div>
  </div>
{/foreach}

{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

  <div>
    <span>{$form.favorite_color.label}</span>
    <span>{$form.favorite_color.html}</span>
  </div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
<br><br>
<h3>LibreOffice Mail Merge Tokens</h3>
<div id="Event" class="boxBlock">
  <table class="report-layout">
    <tbody>
      <tr id="row_1" class="crm-report-instanceList">
        <td><strong>Available Tokens</strong></td>
        <td><strong>If Table not used</strong></td>
        <td><strong>If Table used </strong></td>
        {foreach from=$availableTokens key=tokenKey item=tokenValue}
      </tr>
      <tr id="row_2" class="crm-report-instanceList">
        <td>{$tokenValue.text}</td>
        <td>{$tokenValue.var_name}</td>
        <td>{$tokenValue.var_name_table}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
</div>

 {*<script type="text/javascript">
  cj( document ).ready(function() {
    var temp = cj('.crm-msg-template-form-block-attachment').find('.crm-accordion-header').text('Attachment(WordMailMerge)');
    alert(temp);
  });
 </script>*}

{literal}
  <script type="text/javascript">
    // hide 'Done' button by default
    cj( ".wm-done" ).hide();
    // Display 'Done' button, if print letter is clicked
    cj( ".wm-print-letters" ).click(function() {
      setTimeout(function(){
        cj( ".wm-done" ).show();
      }, 2000);
    });
  </script>
{/literal}