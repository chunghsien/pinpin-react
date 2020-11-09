## 安裝

 ** PHP部分 `PHP 7.2+` 其他請見 `./composer.json` **

> ** 前端部分 , ** `node.js 12.18.2 LTS+` ** 套件管理使用 ** `npm` **, 其他套件請見 ** `./package.json`

> ** Mkdocs 需安裝 ** `python`

```
    $ git clone https://github.com/chunghsien/pinpin_react <project>

    $ mkdocs new <project>

    $ cd <project>

    $ composer install

    $ npm install

    $ php smith migrate:install

    $ php smith migrate

    $ php smith users:permission

    $ php smith users:administrator 

    # <系統會將最高權限管理員的密碼儲存在 ./storage/admin_init_<data>.json>
```


 ** 如要查看說明可以使用 MKDocs **

```
    $ mkdocs serve

    # <開啟瀏覽器 http://127.0.0.1:8000 就可以瀏覽說明了>
```