# Pinpin react

CMS & eCommrece 快速開發應用

## bug fixed

> `Next.js v10.0.1 搭配webpack v5.4.0 出現的bug`

>> `$ cd node_modules/next/node_modules/`

>> `修改 package.json file`

>> `替換 "webpack-sources": "1.4.3" => "webpack-sources": "^1.4.3"`

>> `替換 "pnp-webpack-plugin": "1.6.4" => "pnp-webpack-plugin": "^1.6.4"`

>> `替換 "webpack": "4.44.2" => "webpack": "^4.44.2"`

>> `替換 "webpack-sources": "1.4.3" => "webpack": "^1.4.3"`

>> `$ npm update`

## 詳見 MKDocs

 ** 安裝 `phthon` 及 `pip` ** <https://www.python.org/>

```
$ git clone https://github.com/chunghsien/pinpin_react <project>

$ mkdocs new <project>

$ cd <project>

$ composer install

$ npm install

$ mkdocs serve
```

#### 中文多語政策 ####

> PHP mezzio routes

> > `/site[/{lang}[/{page}[/{id}]]]` ** zh_TW, zh_HK, zh_CN **

> HTML and ReactJS

>> 會吃 `<html lang={lng}>` ** lng (zh-TW, zh-HK, zh-CN) **