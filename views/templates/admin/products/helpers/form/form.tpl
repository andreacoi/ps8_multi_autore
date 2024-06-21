<style>
img.top-logo {
    display:none;
  }
</style>
<div class="panel">
    <h3>Autori del libro</h3>
    <div class="form-group">
        <label for="add-author">{l s='Aggiungi autore a questo libro' d='Modules.AuthorsManager.Admin'}</label>
        <select id="add-author" class="form-control">
            <option value="">{l s='Seleziona uno degli autori presenti' d='Modules.AuthorsManager.Admin'}</option>
            {foreach from=$all_authors item=all_author}
                <option value="{$all_author.id_author}">{$all_author.first_name} {$all_author.last_name}</option>
            {/foreach}
        </select>
        <button type="button" class="btn btn-primary" id="add-author-btn">{l s='Aggiungi autore a questo libro' d='Modules.AuthorsManager.Admin'}</button>
    </div>
    <div id="authors-list">
        {foreach from=$authors item=author}
            <div class="author-item" data-id="{$author.id_author}">
                <input type="hidden" name="authors[]" value="{$author.id_author}" />
                <input type="hidden" name="contribution_types[]" value="{$author.contribution_type}" />
                <p>
                    <strong>{$author.first_name} {$author.last_name}</strong> - 
                    <select class="form-control" name="contribution_types[]">
                        <option value="author" {if $author.contribution_type == 'author'}selected{/if}>{l s='Autore' d='Modules.AuthorsManager.Admin'}</option>
                        <option value="co-author" {if $author.contribution_type == 'co-author'}selected{/if}>{l s='Co-Autore' d='Modules.AuthorsManager.Admin'}</option>
                        <option value="curator" {if $author.contribution_type == 'curator'}selected{/if}>{l s='Curatore' d='Modules.AuthorsManager.Admin'}</option>
                        <option value="editor" {if $author.contribution_type == 'editor'}selected{/if}>{l s='Editore' d='Modules.AuthorsManager.Admin'}</option>
                    </select>
                    <button type="button" class="btn btn-danger remove-author">{l s='Rimuovi autore' d='Modules.AuthorsManager.Admin'}</button>
                </p>
            </div>
        {/foreach}
    </div>
</div>

<script type="text/javascript">
    

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-author-btn').addEventListener('click', function() {
        var authorSelect = document.getElementById('author-select');
        var id_author = authorSelect.value;
        var author_name = authorSelect.options[authorSelect.selectedIndex].text;

        var contributionTypeSelect = document.getElementById('contribution-type-select');
        var contribution_type = contributionTypeSelect.value;
        var contribution_type_text = contributionTypeSelect.options[contributionTypeSelect.selectedIndex].text;

        if (id_author && contribution_type) {
            var authorEntry = document.createElement('div');
            authorEntry.className = 'author-entry';

            var options = [
                '<option value="author"' + (contribution_type === 'author' ? ' selected' : '') + '>Author</option>',
                '<option value="co-author"' + (contribution_type === 'co-author' ? ' selected' : '') + '>Co-Author</option>',
                '<option value="curator"' + (contribution_type === 'curator' ? ' selected' : '') + '>Curator</option>',
                '<option value="editor"' + (contribution_type === 'editor' ? ' selected' : '') + '>Editor</option>',
            ].join('');

            authorEntry.innerHTML = `
                <input type="hidden" name="authors[`+ id_author +`][id_author]" value="`+ id_author +`" />
                <input type="hidden" name="authors[`+ id_author +`][contribution_type]" value="`+ contribution_type +`" />
                <span>`+ author_name +` 
                    <select name="authors[`+ id_author +`][contribution_type]">
                        `+ options +`
                    </select>
                </span>
                <button type="button" class="remove-author-btn">Remove</button>
            `;

            document.getElementById('authors-list').appendChild(authorEntry);
        }
    });

    document.getElementById('authors-list').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('remove-author-btn')) {
            event.target.closest('.author-entry').remove();
        }
    });
});
</script>


