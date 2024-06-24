{extends file='page.tpl'}

{block name='page_content'}
    <div class="authors-list">
        <h1>{l s='Authors' d='Modules.AuthorsManager'}</h1>
        {foreach from=$authors item=author}
            <div class="author">
                <h2><a href="{$link->getModuleLink('authorsmanager', 'authordetails', ['id_author' => $author.id_author])|escape:'html':'UTF-8'}">
                    {$author.first_name|escape:'html':'UTF-8'} {$author.last_name|escape:'html':'UTF-8'}
                </a></h2>
                <p>{$author.biography|escape:'html':'UTF-8'}</p>
            </div>
        {/foreach}
    </div>
{/block}
