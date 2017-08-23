uk.co.vedaconsulting.module.wordmailmerge
=========================================

## Description

Allows you to produce Word Mail Merged documents from CiviCRM.

## How to Install

1. Download extension from https://github.com/veda-consulting/uk.co.vedaconsulting.module.wordmailmerge/releases/latest.
2. Unzip / untar the package and place it in your configured extensions directory.
3. When you reload the Manage Extensions page the new “Word Mail Merge” extension should be listed with an install link.
4. Proceed with install.

##Setup

1. Create your Template Word document using the necessary tokens. (Sample 'template.docx' & available 'token.odt' have been provided in the extension directory).
2. Navigate to 'Mailings'->'Message Templates'.
3. Click 'Add Message Template'.
4. Give a name for your new template in 'Message Title' field in the New Message Template form.

### Version 4.6
5. In 'Attachment( for WordMailMerge)' section, attach the template file created.
6. Click 'Save'.

### Version 4.7
5. Tick the 'Upload Document' checkbox in Source section.
6. Attach your docx template in 'Upload Document' section.
7. Make sure 'Wordmailmerge template' & 'Enabled?' checkboxes are ticked to use the Message Template as a Wordmailmerge template.
8. Click 'Save'.

## Usage
1. Letters can be produced from the following search results
   - 'Search'->'Find Contacts' or 'Search'->'Advanced Search'.
   - 'Memberships' -> 'Find memberships' (** Use this search to print letters with membership related tokens **)
2. Select the records that you want to send the letters from one of the above search results.
3. Select 'Word Mail Merge' in the Actions drop down.
4. All the available tokens will be listed in the Word Mail Merge screen to double check the usage of tokens in the docx.
4. If you are happy with all your tokens, select the Message Template that you created from the Message Template drop down.
5. Click 'Confirm Action'.
6. Choose your destination to save the exported Document.

### Changelog
Ver 2.1 : 'Wordmailmerge Letter' activity will be recorded against each contact.
