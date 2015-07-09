{* template block that contains the new field *}
  {*<div>Attach File:{$form.uploadFile.html}{$form.attachDesc.html} </div>*}
  <div class="crm-msg-template-form-block-attachment">
  {if $action eq 1 || $action eq 2}
     {include file="CRM/Form/attachment.tpl"}
 
 {if $defaults}
    <table>
      <tr>
        <td class="label">{ts}Current Attachment(s){/ts}</td>
            <td class="view-value">
          {foreach from=$defaults key=attKey item=attVal}
          <div id="attachStatusMesg" class="status hiddenElement"></div>
                <div class="crm-attachment-wrapper crm-entity" id="attachFileRecord_{$attVal.fileID}">
                  <strong><a class="crm-attachment" href="{$attVal.url}">{$attVal.cleanName}</a></strong>
                  {if $attVal.description}&nbsp;-&nbsp;{$attVal.description}{/if}
                  {if $attVal.deleteURLArgs}
<!--                    <a href="#" class="crm-hover-button delete-attachment" data-filename="{$attVal.cleanName}" data-args="{$attVal.deleteURLArgs}" title="{ts}Delete File{/ts}"><span class="icon delete-icon"></span></a> -->                  
                   <a href="javascript:void(0)" onclick="showDeleteAttachment('{$attVal.cleanName}', '{$attVal.deleteURLArgs}', {$attVal.fileID}, '#attachStatusMesg', '#attachFileRecord_{$attVal.fileID}'); return false;" title="{ts}Delete this attachment{/ts}"><span class="icon red-icon delete-icon" style="margin:0px 0px -5px 20px" title="{ts}Delete this attachment{/ts}"></span></a>
                  {/if}
                  {if !empty($attVal.tag)}
                    <br/>
                    {ts}Tags{/ts}: {$attVal.tag}
                    <br/>
                  {/if}
                </div>
          {/foreach}
            </td>
        </tr>
        </table>
  {/if}
  {/if}
 </div>
{* reposition the above block after #someOtherBlock *}
{literal}
<script type="text/javascript">
  cj('.crm-msg-template-form-block-attachment').insertAfter(cj("#pdf_format_id").parent().parent().parent());
  cj('.crm-msg-template-form-block-attachment').find('.crm-accordion-header').text('Attachment( for WordMailMerge)');
  cj(document).ready(function(){
    cj('.crm-msg-template-form-block-attachment').find('.crm-accordion-wrapper').removeClass();
    cj('.crm-msg-template-form-block-attachment').children().attr('class', 'crm-accordion-wrapper');
    cj('.crm-msg-template-form-block-attachment').find('.crm-accordion-body').show();
  });
  function hideStatusAttachment( divName ) {
    cj( divName ).hide( );
  }

  function showDeleteAttachment(fileName, postURLData, fileID, divName, divFile) {
    var confirmMsg = '<div>{/literal}{ts escape="js"}Are you sure you want to delete attachment: {/ts}{literal}' + fileName + '&nbsp; <a href="#" onclick="deleteAttachment( \'' + postURLData + '\',' + fileID + ',\'' + divName + '\', \'' + divFile + '\' ); return false;" style="text-decoration: underline;">{/literal}</div><div>{ts escape='js'}Yes{/ts}{literal}</a>&nbsp;&nbsp;&nbsp;<a href="#" onclick="hideStatusAttachment( \'' + divName + '\' ); return false;" style="text-decoration: underline;">{/literal}{ts escape='js'}No{/ts}{literal}</a></div>';
    cj(divName).show().html(confirmMsg);
  }

  function deleteAttachment(postURLData, fileID, divName, divFile) {
    var postUrl = {/literal}"{crmURL p='civicrm/file/delete' h=0 }"{literal};
    cj.ajax({
      type: "POST",
      data:  postURLData,
      url: postUrl,
      success: function (html) {
        var resourceBase   = {/literal}"{$config->resourceBase}"{literal};
        var successMsg = '{/literal}{ts escape="js"}The selected attachment has been deleted.{/ts}{literal}';
        cj(divFile + ',' + divName).hide();
        CRM.alert(successMsg, '{/literal}{ts escape="js"}Removed{/ts}{literal}', 'success');
      }
    });
  }
</script>
{/literal}
