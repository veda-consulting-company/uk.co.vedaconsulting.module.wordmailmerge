<table class="form-layout">
      <tr>
        <td>{$form.title.label}</td>
        <td>{$form.title.html}</td>
      </tr>
      <tr>
          <td>{$form.uploadFile.label}</td>
          <td>{$form.uploadFile.html}<br />
            <div class="description">{ts}File format must be .ods or .xlxs{/ts}</div>
            {ts 1=$uploadSize}Maximum Upload File Size: %1 MB{/ts}
          </td>
      </tr>
      <tr>
        <td>{$form.mail_merge.label}</td>
        <td>{$form.mail_merge.html}</td>
      </tr>
      <tr>
        <td>{$form.is_active.label}</td>
        <td>{$form.is_active.html}</td>
      </tr>
</table>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
