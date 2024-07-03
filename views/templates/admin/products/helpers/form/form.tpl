<style>
img.top-logo {
    display:none;
  }
</style>

<div id="authors-manager">
    <div>
        <label for="author-select">Seleziona autore</label>
        <select class="form-control" id="author-select">
            {foreach from=$all_authors item=author}
                <option value="{$author.id_author}">{$author.first_name} {$author.last_name}</option>
            {/foreach}
        </select>
        
        <label for="contribution-type-select">Tipo di contribuzione</label>
        <select class="form-control" id="contribution-type-select">
            <option value="author">Autore</option>
            <option value="curator">Curatore (old style, a cura di)</option>
            <option value="editor">Editor (nuova versione internazionale)</option>
        </select>
        
        <button type="button btn btn-primary" id="add-author-btn">Aggiungi autore</button>
    </div>
   <br /><br /> 
    <div id="authors-list">
        {foreach from=$authors item=author}
            <div class="author-entry">
                <input type="hidden" name="authors[{$author.id_author}][id_author]" value="{$author.id_author}" />
                <input type="hidden" name="authors[{$author.id_author}][contribution_type]" value="{$author.contribution_type}" />
                <span>{$author.first_name} {$author.last_name} 
                    <select class="form-control" name="authors[{$author.id_author}][contribution_type]">
                        <option value="author" {if $author.contribution_type == 'author'}selected{/if}>Autore</option>
                        <option value="curator" {if $author.contribution_type == 'curator'}selected{/if}>Curatore (old style, a cura di)</option>
                        <option value="editor" {if $author.contribution_type == 'editor'}selected{/if}>Editor (nuova versione internazionale)</option>
                    </select>
                </span>
                <button type="btn btn-small btn-danger button" class="remove-author-btn">Remove</button>
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
                '<option value="author"' + (contribution_type === 'author' ? ' selected' : '') + '>Autore</option>',
                '<option value="curator"' + (contribution_type === 'curator' ? ' selected' : '') + '>Curatore (old style, a cura di)</option>',
                '<option value="editor"' + (contribution_type === 'editor' ? ' selected' : '') + '>Editor (nuova versione internazionale)</option>',
            ].join('');

            authorEntry.innerHTML = `
                <input type="hidden" name="authors[`+ id_author +`][id_author]" value="`+ id_author +`" />
                <input type="hidden" name="authors[`+ id_author +`][contribution_type]" value="`+ contribution_type +`" />
                <span>`+ author_name +` 
                    <select class="form-control" name="authors[`+ id_author +`][contribution_type]">
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


