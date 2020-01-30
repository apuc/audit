<?php

namespace frontend\modules\site\models;

use common\classes\Debug;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Site;

/**
 * SiteSearch represents the model behind the search form of `common\models\Site`.
 */
class SiteSearch extends Site
{
    public $theme;
    public $external_links;
    public $ip;
    public $dns;
    public $server_response_code;
    public $size;
    public $loading_time;
    public $anchor;
    public $comment;
    public $google_indexing;
    public $yandex_indexing;
    public $google_indexed_pages;
    public $iks;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'creation_date', 'expiration_date', 'theme_id', 'server_response_code', 'size', 'loading_time', 'google_indexed_pages', 'iks'], 'integer'],
            [['google_indexing', 'yandex_indexing'], 'boolean'],
            [['name', 'registrar', 'states', 'theme', 'external_links', 'ip', 'dns', 'anchor', 'redirect', 'comment', 'title'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Site::find()
            ->leftJoin('site_themes', 'site.id = site_themes.site_id')
            ->leftJoin('theme', 'site_themes.theme_id = theme.id')
            ->leftJoin('comments', 'site.id = comments.site_id')
            ->leftJoin('url', 'site.id = url.site_id')
            ->leftJoin('audit', 'url.id = audit.url_id')
            ->leftJoin('external_links', 'audit.id = external_links.audit_id')
            ->leftJoin('dns', 'site.id = dns.site_id')
            ->leftJoin('indexing', 'site.id = indexing.site_id')
            ->orderBy('site.id desc')
            ->groupBy('site.name');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [1, 500],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'creation_date' => $this->creation_date,
            'expiration_date' => $this->expiration_date,
            'theme_id' => $this->theme_id,
            'user_id' => Yii::$app->user->identity->id,
            'size' => $this->size,
            'loading_time' => $this->loading_time,
            'server_response_code' => $this->server_response_code,
            'indexing.google_indexed_pages' => $this->google_indexed_pages,
            'indexing.iks' => $this->iks,
            'indexing.google_indexing' => $this->google_indexing,
            'indexing.yandex_indexing' => $this->yandex_indexing
        ]);

        $query->andFilterWhere(['like', 'site.name', $this->name])
            ->andFilterWhere(['like', 'redirect', $this->redirect])
            ->andFilterWhere(['like', 'registrar', $this->registrar])
            ->andFilterWhere(['like', 'states', $this->states])
            ->andFilterWhere(['like', 'theme.name', $this->theme])
            ->andFilterWhere(['like', 'external_links.acceptor', $this->external_links])
            ->andFilterWhere(['like', 'external_links.anchor', $this->anchor])
            ->andFilterWhere(['like', 'dns.ip', $this->ip])
            ->andFilterWhere(['like', 'dns.target', $this->dns])
            ->andFilterWhere(['like', 'comments.comment', $this->comment])
            ->andFilterWhere(['like', 'site.title', $this->title]);

        return $dataProvider;
    }
}
