# CODAIMPORTER FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

This plugin for Dolibarr allows to import CODA files into the accountancy of Dolibarr.

## Features

- [X]  - Imports CODA files
- [X]  - Saves the payment into the bank account
- [ ]  - Lists all the imported coda files
- [X]  - Marks the invoice, bill, ... as paied

### Mapping
A transaction will be mapped 

|   | Bingo | Success | good |  Warning | Danger  | 
|---|--- |--- |--- |---|---|
| Amount  |  X | X | X |  X | X |
| third party bank account  | X |  |   |   |
| third party name  |  X | X | X |   | Or - X
| Communication reference  |  X | X |  | X  | Or - X

In all other cases there is no mapping and the mapping has to be defined manually!

## Translations

Translations can be define manually by editing files into directories *langs*.


## Installation

### From the ZIP file and GUI interface

- If you get the module in a zip file (like when downloading it from the market place [Dolistore](https://www.dolistore.com)), go into
menu ```Home - Setup - Modules - Deploy external module``` and upload the zip file.

Note: If this screen tell you there is no custom directory, check your setup is correct:

- In your Dolibarr installation directory, edit the ```htdocs/conf/conf.php``` file and check that following lines are not commented:

    ```php
    //$dolibarr_main_url_root_alt ...
    //$dolibarr_main_document_root_alt ...
    ```

- Uncomment them if necessary (delete the leading ```//```) and assign a sensible value according to your Dolibarr installation

    For example :

    - UNIX:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';
        ```

    - Windows:
        ```php
        $dolibarr_main_url_root_alt = '/custom';
        $dolibarr_main_document_root_alt = 'C:/My Web Sites/Dolibarr/htdocs/custom';
        ```

### From a GIT repository

- Clone the repository in ```$dolibarr_main_document_root_alt/codaimporter```

```sh
cd ....../custom
git clone git@github.com:gitlogin/codaimporter.git codaimporter
```

### <a name="final_steps"></a>Final steps

From your browser:

  - Log into Dolibarr as a super-administrator
  - Go to "Setup" -> "Modules"
  - You should now be able to find and enable the module


## Authors

- [@laudeco](https://www.github.com/laudeco)

