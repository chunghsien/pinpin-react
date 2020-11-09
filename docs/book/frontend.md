## ReactJs

#### 佈署與開發監控

**watch**

>  **開發階段使用(會監控修改的檔案即時做更新reload網頁就可以看到)**
```
    $ cd <進入工作目錄>
    $ npm run watch
```

**build**

> **發布(最佳化)使用**

```
    $ cd <進入工作目錄>
    $ npm run build
```

#### 中文多語政策

> 會吃 `<html lang={lng}>` ** lng (zh-TW, zh-HK, zh-CN) **

>變數 `{lang}` 會由PHP送至前端樣板內

#### bug fixed

```
    ./node_modules/@coreui/react/es/template/CSidebarNavItem.js #line: 41
    
    ## ? icon : /*#__PURE__*/React.createElement(CIcon, iconProps(icon))
    ##  修改成 ? icon : null
```  