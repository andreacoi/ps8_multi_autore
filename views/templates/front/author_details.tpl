
{if $author}
    <div class="author-details">
        <h1>{$author.first_name} {$author.last_name}</h1>
        <p>{$author.biography}</p>
        
        {if $books}
            <h2>{l s='Books by this author' d='Modules.AuthorsManager'}</h2>
            <ul>
                {foreach from=$books item=book}
                    <li>
                        <a href="{$link->getProductLink($book.id_product)}">{$book.name}</a>
                    </li>
                {/foreach}
            </ul>
        {else}
            <p>{l s='No books found for this author.' d='Modules.AuthorsManager'}</p>
        {/if}
    </div>
{else}
    <p>{l s='Author not found.' d='Modules.AuthorsManager'}</p>
{/if}
