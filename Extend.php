<?php

namespace SoosyzeExtension\FaqSimple;

use Psr\Container\ContainerInterface;
use Queryflatfile\TableBuilder;
use Soosyze\Components\Template\Template;

class Extend extends \SoosyzeCore\System\ExtendModule
{
    protected $pathContent;

    public function __construct()
    {
        $this->pathContent = __DIR__ . '/Views/Content/';
    }

    public function boot()
    {
        $this->loadTranslation('fr', __DIR__ . '/Lang/fr/main.json');
    }

    public function getDir()
    {
        return __DIR__ . '/composer.json';
    }

    public function hookInstall(ContainerInterface $ci)
    {
        if ($ci->module()->has('User')) {
            $this->hookInstallUser($ci);
        }
    }

    public function hookInstallUser(ContainerInterface $ci)
    {
        $ci->query()
            ->insertInto('role_permission', [ 'role_id', 'permission_id' ])
            ->values([ 2, 'node.show.published.page_faq' ])
            ->values([ 1, 'node.show.published.page_faq' ])
            ->execute();
    }

    public function hookUninstall(ContainerInterface $ci)
    {
        if ($ci->module()->has('User')) {
            $this->hookUninstallUser($ci);
        }
    }

    public function hookUninstallUser(ContainerInterface $ci)
    {
        $ci->query()
            ->from('role_permission')
            ->delete()
            ->where('permission_id', 'like', '%page_faq%')
            ->execute();
    }

    public function install(ContainerInterface $ci)
    {
        $ci->schema()
            ->createTableIfNotExists('entity_page_faq', static function (TableBuilder $table) {
                $table->increments('page_faq_id')
                ->text('body');
            })
            ->createTableIfNotExists('entity_faq', static function (TableBuilder $table) {
                $table->increments('faq_id')
                ->integer('page_faq_id')
                ->integer('weight')->valueDefault(1)
                ->string('question', 512)
                ->text('response');
            });

        $ci->query()
            ->insertInto('field', [ 'field_name', 'field_type' ])
            ->values([ 'field_name' => 'question', 'field_type' => 'text' ])
            ->values([ 'field_name' => 'response', 'field_type' => 'textarea' ])
            ->values([ 'field_name' => 'faq', 'field_type' => 'one_to_many' ])
            ->execute();

        $idBody     = $ci->query()->from('field')->where('field_name', 'body')->fetch()[ 'field_id' ];
        $idRelation = $ci->query()->from('field')->where('field_name', 'faq')->fetch()[ 'field_id' ];
        $idQuestion = $ci->query()->from('field')->where('field_name', 'question')->fetch()[ 'field_id' ];
        $idResponse = $ci->query()->from('field')->where('field_name', 'response')->fetch()[ 'field_id' ];
        $idWeight   = $ci->query()->from('field')->where('field_name', 'weight')->fetch()[ 'field_id' ];

        $ci->query()
            ->insertInto('node_type', [
                'node_type',
                'node_type_name',
                'node_type_description',
                'node_type_icon',
                'node_type_color'
            ])
            ->values([
                'node_type'             => 'page_faq',
                'node_type_name'        => 'FAQ',
                'node_type_description' => 'Create your question and answer page.',
                'node_type_icon'        => 'fa fa-question',
                'node_type_color'       => '#7ff6ff'
            ])
            ->execute();

        $ci->query()
            ->insertInto('node_type_field', [
                'node_type', 'field_id', 'field_weight', 'field_label', 'field_rules',
                'field_option', 'field_default_value', 'field_show', 'field_show_label'
            ])
            ->values([
                'faq', $idQuestion, 1, 'Question', 'required|string|max:512', '',
                null, true, false
            ])
            ->values([
                'faq', $idResponse, 2, 'Response', '!required|string', '', null,
                true, false
            ])
            ->values([
                'faq', $idWeight, 3, 'Weight', 'required|int|min:1', '', '1', false,
                false
            ])
            ->values([
                'page_faq', $idBody, 1, 'Body', '!required|string', '', null, true,
                false
            ])
            ->values([
                'page_faq', $idRelation, 2, 'List of questions and answers', 'required|array',
                json_encode([
                    'relation_table' => 'entity_faq',
                    'local_key'      => 'page_faq_id',
                    'foreign_key'    => 'page_faq_id',
                    /* 'asc, desc, weight */
                    'sort'           => 'weight',
                    'order_by'       => 'weight',
                    'count'          => 5,
                    'field_show'     => 'question'
                ]), null, true, true
            ])
            ->execute();
    }

    public function seeders(ContainerInterface $ci)
    {
        $ci->query()
            ->insertInto('entity_page_faq', [ 'body' ])
            ->values([
                (new Template('article_1.php', $this->pathContent))->render()
            ])
            ->execute();

        $time = (string) time();
        $ci->query()
            ->insertInto('node', [
                'title', 'type', 'date_created', 'date_changed', 'node_status_id',
                'entity_id'
            ])
            ->values([
                'FAQ 1', 'page_faq', $time, $time, 3, 1
            ])
            ->execute();

        $ci->query()
            ->insertInto('entity_faq', [ 'question', 'response', 'page_faq_id' ])
            ->values([ 'Je suis une question', 'je suis la réponse', 1 ])
            ->values([ 'Je suis une question', 'je suis la réponse', 1 ])
            ->execute();
    }

    public function uninstall(ContainerInterface $ci)
    {
        $ci->node()->deleteAliasByType('page_faq');
        $ci->node()->deleteByType('page_faq');
    }
}
