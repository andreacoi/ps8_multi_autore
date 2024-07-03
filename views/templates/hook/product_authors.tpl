{foreach from=$authors item=author}
  <a href="/las/autore/{$author.id_author}">{$author.first_name} {$author.last_name}</a>{if $author@last} {else}, {/if}
{/foreach}
{$contribuzione}
