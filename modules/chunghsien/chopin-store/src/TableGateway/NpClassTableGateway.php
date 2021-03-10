<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Psr\Http\Message\ServerRequestInterface;

class NpClassTableGateway extends AbstractTableGateway
{

    use \App\Traits\I18nTranslatorTrait;
    
    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'np_class';

    public function getIds(ServerRequestInterface $request)
    {
        $language_id = $request->getAttribute('language_id');
        $locale_id = $request->getAttribute('locale_id');
        $select = $this->getSql()->select();
        $select->columns([
            'id'
        ]);
        $where = $select->where;
        $where->equalTo('language_id', $language_id);
        $where->equalTo('locale_id', $locale_id);
        $where->isNull('deleted_at');
        $select->order([
            'sort ASC',
            'id DESC'
        ]);
        $resultSet = $this->selectWith($select);
        $result = [];
        foreach ($resultSet as $row) {
            $result[] = [
                "params" => [
                    "type" => $row->id
                ]
            ];
        }
        return $result;
    }

    public function getType()
    {
        return [
            [
                "params" => [
                    "type" => "all"
                ]
            ],
            [
                "params" => [
                    "type" => "new"
                ]
            ],
            [
                "params" => [
                    "type" => "hot"
                ]
            ],
            [
                "params" => [
                    "type" => "recommend"
                ]
            ],
            [
                "params" => [
                    "type" => "spec_group"
                ]
            ],
            [
                "params" => [
                    "type" => "item_detail"
                ]
            ],
        ];
    }

    public function getCategory(ServerRequestInterface $request, $isSpecialAdd = true)
    {
        // $request->withAttribute('html_lang', $html_lang)
        $localeCode = $request->getAttribute('html_lang');
        $php_lang = $request->getAttribute('php_lang');
        $language_id = $request->getAttribute('language_id');
        $locale_id = $request->getAttribute('locale_id');
        $select = $this->getSql()->select();
        $select->columns([
            'id',
            'name'
        ]);
        $where = $select->where;
        $where->equalTo('language_id', $language_id);
        $where->equalTo('locale_id', $locale_id);
        $where->isNull('deleted_at');
        $select->order([
            'sort ASC',
            'id DESC'
        ]);
        $resultSet = $this->selectWith($select);
        $result = [];
        $slug = $request->getAttribute('method_or_id');
        if ($isSpecialAdd) {
            $this->initTranslator();
            $this->translator->addTranslationFilePattern('phpArray', PROJECT_DIR.'/resources/languages', '%s/site-translation.php', 'site-translation');
            $this->translator->setLocale($php_lang);
            $result[] = [
                'id' => 'all',
                'name' => $this->translator->translate('category_all', 'site-translation'),
                'uri' => "/{$localeCode}/category/all",
                'active' => $slug == 'all',
                ];
            $result[] = [
                'id' => 'new',
                'name' => $this->translator->translate('category_new', 'site-translation'),
                'uri' => "/{$localeCode}/category/new",
                'active' => $slug == 'new',
                ];
            $result[] = [
                'id' => 'hot',
                'name' => $this->translator->translate('category_hot', 'site-translation'),
                'uri' => "/{$localeCode}/category/hot",
                'active' => $slug == 'hot',
            ];
            $result[] = [
                'id' => 'recommend',
                'name' => $this->translator->translate('category_recommend', 'site-translation'),
                'uri' => "/{$localeCode}/category/recommend",
                'active' => $slug == 'recommend',
                ];
            
        }
        foreach ($resultSet as $row) {
            $data = $row->toArray();
            $data['uri'] = "/{$localeCode}/category/" . $data['id'];
            $data['active'] = ($slug == $data['id']);
            $result[] = $data;
        }
        return $result;
    }
}
