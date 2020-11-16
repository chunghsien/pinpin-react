# Pinpin react

CMS & eCommrece 快速開發應用

![image](https://github.com/chunghsien/pinpin-react/blob/master/img1.png)

![image](https://github.com/chunghsien/pinpin-react/blob/master/img2.png)

![image](https://github.com/chunghsien/pinpin-react/blob/master/img3.png)

### 安裝說明
```
# 安裝 `phthon` 及 `pip` ** <https://www.python.org/>
$ git clone https://github.com/chunghsien/pinpin_react <project>
$ mkdocs new <project>
$ cd <project>
$ composer install
$ npm install
$ mkdocs serve
```

### 編譯指令說明
```
$ php smith migration:install #創建遷移記錄資料表，需先自行建立資料庫及參數設定
  # ./config/config/db.development.php, ./config/config/db.test.php,
  # ./config/config/db.production.php
$ php smith migrate #創建資料表
  # .最高權線管理者帳密會產生在./storage/admin_init_<date>.json
$ php smith users:permission --source=./modules/App/config/admin.navigation.php #建立後臺權限
$ php smith users:administrator #建立最高管理員權限
$ npm run watch #網頁後臺系統程式碼監控
$ npm run build #網頁後臺系統發佈編譯(有優化及壓縮)
$ num next-dev #網頁前臺程式碼監控
$ num next-dev #網頁前臺程式碼發佈編譯(有優化及壓縮)
$ php smith next-js-dispatch #佈署靜態內容至後端(做SSR)
```
<!--
#### 中文多語政策 ####

> PHP mezzio routes

> > `/site[/{lang}[/{page}[/{id}]]]` ** zh_TW, zh_HK, zh_CN **

> HTML and ReactJS

>> 會吃 `<html lang={lng}>` ** lng (zh-TW, zh-HK, zh-CN) **
-->
