{extends file='page.tpl'}
{block name='page_content'}
    <div class="author-details">
        <h1 class="text-center">{$author.first_name} {$author.last_name}</h1>
        <p class="text-center">{$author.biography}</p>

        <hr />

        <div class="author-books">
            <h2 class="text-center">Libri di {$author.first_name} {$author.last_name}</h2>
            {if $products}
                {include file="catalog/_partials/products.tpl" listing=$listing}
            {else}
                <p class="text-center">Nessun libro disponibile.</p>
            {/if}
        </div>
    </div>
{/block}

