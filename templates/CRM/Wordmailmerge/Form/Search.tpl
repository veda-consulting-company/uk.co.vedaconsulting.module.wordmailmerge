{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}

{* Main template for basic search (Find Contacts) *}
{include file="CRM/Contact/Form/Search/Intro.tpl"}
<div class="crm-form-block crm-search-form-block">
{* This section handles form elements for search criteria *}
  <div id="searchForm">
    <div class="crm-accordion-wrapper crm-case_search-accordion {if $rows}collapsed{/if}">
      <div class="crm-accordion-header crm-master-accordion-header">
        {ts}Edit Search Criteria{/ts}
      </div><!-- /.crm-accordion-header -->
      <div class="crm-accordion-body">
      {foreach from=$elementNames item=elementName}
        <div class="crm-section">
          <div class="label">{$form.$elementName.label}</div>
          <div class="content">{$form.$elementName.html}</div>
          <div class="clear"></div>
        </div>
      {/foreach}
      <div id="contact_count"></div>
      <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
      </div><!-- /.crm-accordion-body -->
    </div>
  </div>
</div>
<div class="crm-content-block">
{if $rowsEmpty && $resetResult}
  <div class="crm-results-block crm-results-block-empty">
    <div class="messages status no-popup">
      <div class="icon inform-icon"></div>&nbsp;
        {ts}No matches found.{/ts}
        {ts}Suggestions:{/ts}
        <ul>
        <li>{ts}check your spelling{/ts}</li>
        <li>{ts}try a different spelling or use fewer letters{/ts}</li>
        </ul>
    </div>
  </div>
{elseif $rows}
  <div class="crm-results-block">

      {include file="CRM/common/pagerAToZ.tpl"}
 <!-- {* <a href="#" class="crm-selection-reset crm-hover-button"><span class="icon close-icon"></span> {ts}Reset all selections{/ts}</a> *} -->

      <table summary="{ts}Search results listings.{/ts}" class="selector row-highlight">
        <thead class="sticky">
          <tr>
            <!-- <th scope="col" title="Select All Rows">{$form.toggleSelect.html}</th> -->
            {foreach from=$columnHeaders item=header}
              <th scope="col">
                {$header}
              </th>
            {/foreach}
          </tr>
        </thead>

        {counter start=0 skip=1 print=false}

        {foreach from=$rows item=row}
         <tr id="rowid{$row.contact_id}" class="{cycle values='odd-row,even-row'}">
            {assign var=cbName value=$row.checkbox}
            <!-- <td>{$form.$cbName.html}</td> -->
            <td>{$row.name}</td>
            <td>{$row.adult_name}</td>
            <td>{$row.membership_type}</td>
            <td>{$row.address}</td>
            <td>{$row.end_date}</td>
            <td>{$row.last_contribution}</td>
            <!-- <td style='width:125px;'>{$row.action|replace:'xx':$row.contact_id}</td> -->
         </tr>
        {/foreach}
      </table>
      {include file="CRM/common/pager.tpl" location="bottom"}
       </div>
    {* END Actions/Results section *}
    </div>
{else}
    <div class="spacer">&nbsp;</div>
{/if}
</div>


{literal}
<script type="text/javascript">
  cj(document).ready(function(){
    var xhr;
    cj('.crm-master-accordion-header').click(function(){
     cj(this).parent().toggleClass('collapsed'); 
    });
    
  });
  
</script>
{/literal}
