{foreach from=$authors item=author}
  <a href="#">{$author.first_name} {$author.last_name}</a>{if $author.contribution_type == 'curator'} (a cura di) {elseif $author.contribution_type == 'editor'} (ed.) {/if}{if $author@last}{else}, {/if}
{/foreach}
