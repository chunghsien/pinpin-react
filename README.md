# Pinpin react

CMS & eCommrece 快速開發應用

![image](https://github.com/chunghsien/pinpin-react/blob/master/img1.png)

![image](https://github.com/chunghsien/pinpin-react/blob/master/img2.png)

![image](https://github.com/chunghsien/pinpin-react/blob/master/img3.png)

### 安裝說明
```
# 安裝 `python` 及 `pip` ** <https://www.python.org/>
$ git clone https://github.com/chunghsien/pinpin_react <project>
$ mkdocs new <project>
$ cd <project>
$ composer install
$ npm install
$ mkdocs serve
```

### 編譯指令說明
```
#創建遷移記錄資料表，需先自行建立資料庫及參數設定
#./config/config/db.development.php
#./config/config/db.test.php
#./config/config/db.production.php
$ php smith migration:install
# .最高權限管理者帳密會產生在./storage/admin_init_<date>.json
$ php smith migrate
#建立後臺權限
$ php smith users:permission --source=./modules/App/config/admin.navigation.php
#建立最高管理員權限
$ php smith users:administrator
#網頁後臺系統程式碼監控
$ npm run watch
#網頁後臺系統發佈編譯(有優化及壓縮)
$ npm run build 
#網頁前臺程式碼監控
$ num next-dev
#網頁前臺程式碼發佈編譯(有優化及壓縮)
$ num next-export
#佈署靜態內容至後端(做SSR)
$ php smith next-js-dispatch
```
<!--
1.電商、餐飲
2.2+級分銷(拆帳)
3.直播系統(整合電商餐飲)
4.APP開發及整合
?.伺服器效能優化、SEO, SEM整合至系統架構內
?.高流量網站研究整合
-->
