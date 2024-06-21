
{if $authors}
    <div class="authors-list">
        <h1>{l s='Authors' d='Modules.AuthorsManager'}</h1>
        <ul>
            {foreach from=$authors item=author}
                <li>
                    <a href="{$link->getModuleLink('authorsmanager', 'authordetails', ['id_author' => $author.id_author])}">
                        {$author.first_name} {$author.last_name}
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
{else}
    <p>{l s='No authors found.' d='Modules.AuthorsManager'}</p>
{/if}
