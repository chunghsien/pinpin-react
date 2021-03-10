<?php
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\Documents\TableGateway\DocumentsTableGateway;

class Chopin_Documents_Insert extends AbstractSeeds
{

    protected $table = 'documents';

    public function run()
    {
        $tableGateway = new DocumentsTableGateway($this->adapter);
        $sets = [
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '首頁',
                'route' => '/zh-TW',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '聯絡我們',
                'route' => '/zh-TW/contact',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '關於我們',
                'route' => '/zh-TW/about',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '購物車',
                'route' => '/zh-TW/cart',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '結帳',
                'route' => '/zh-TW/checkout',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '產品比較',
                'route' => '/zh-TW/compare',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '常見問題',
                'route' => '/zh-TW/faq',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '會員登入 & 註冊',
                'route' => '/zh-TW/login-register',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '會員中心',
                'route' => '/zh-TW/my-account',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '訂單追蹤',
                'route' => '/zh-TW/my-account',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '希望清單',
                'route' => '/zh-TW/wishlist',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '產品分類',
                'route' => '/zh-TW/category',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '門市資訊',
                'route' => '/zh-TW/store-list',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '最新商品',
                'route' => '/zh-TW/category/new',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '人氣熱銷',
                'route' => '/zh-TW/category/hot',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '店長推薦',
                'route' => '/zh-TW/category/recommend',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '產品顯示',
                'route' => '/zh-TW/product',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '最新消息分類',
                'route' => '/zh-TW/news-category',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 1,
                'name' => '最新消息',
                'route' => '/zh-TW/news',
                'allowed_methods' => json_encode([
                    "GET"
                ]),
            ],
        ];
        foreach ($sets as $set)
        {
            if($tableGateway->select($set)->count() == 0)
            {
                $tableGateway->insert($set);
            }
        }
        
    }
}
