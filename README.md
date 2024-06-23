# Modulo PS 8 per associare fino a 10 autori per ciascun libro

Questo modulo segue l'idea fornita da Bompiani, quindi ogni autore ha questi campi:

- Nome;
- Cognome;
- Biografia;
  e viene associato a un libro tramite una FK, specificando il tipo di contribuzione che ha avuto all'interno del libro:
- Autore;
- Co-autore;
- Curatore;
- Editore;

## Operazioni preliminari (setup database)

Queste sono le due query SQL che servono per generare le tabelle:

```
# Creazione tabella autori

CREATE TABLE `ps_author` (
    `id_author` INT(11) NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `biography` TEXT NOT NULL,
    PRIMARY KEY (`id_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Creazione relazione autore libro

CREATE TABLE `ps_product_author` (
    `id_product` INT(10) UNSIGNED NOT NULL,
    `id_author` INT(11) NOT NULL,
    `contribution_type` ENUM('author', 'co-author', 'curator', 'editor') NOT NULL,
    PRIMARY KEY (`id_product`, `id_author`),
    FOREIGN KEY (`id_product`) REFERENCES `ps_product` (`id_product`) ON DELETE CASCADE,
    FOREIGN KEY (`id_author`) REFERENCES `ps_author` (`id_author`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
