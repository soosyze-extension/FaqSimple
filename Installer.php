<?php

namespace SoosyzeExtension\FaqSimple;

use Psr\Container\ContainerInterface;
use Queryflatfile\TableBuilder;
use Soosyze\Components\Template\Template;

class Installer implements \SoosyzeCore\System\Migration
{
    protected $pathContent;

    public function __construct()
    {
        $this->pathContent = __DIR__ . '/Views/Content/';
    }

    public function getDir()
    {
        return __DIR__ . '/composer.json';
    }

    public function install(ContainerInterface $ci)
    {
        $ci->schema()
            ->createTableIfNotExists('entity_page_faq', function (TableBuilder $table) {
                $table->increments('page_faq_id')
                ->text('body');
            })
            ->createTableIfNotExists('entity_faq', function (TableBuilder $table) {
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
            ->values([ 'field_name' => 'weight', 'field_type' => 'number' ])
            ->values([ 'field_name' => 'faq', 'field_type' => 'one_to_many' ])
            ->execute();

        $idBody     = $ci->query()
                ->from('field')
                ->where('field_name', 'body')
                ->fetch()[ 'field_id' ];
        $idRelation = $ci->query()
                ->from('field')
                ->where('field_name', 'faq')
                ->fetch()[ 'field_id' ];
        $idQuestion = $ci->query()
                ->from('field')
                ->where('field_name', 'question')
                ->fetch()[ 'field_id' ];
        $idResponse = $ci->query()
                ->from('field')
                ->where('field_name', 'response')
                ->fetch()[ 'field_id' ];
        $idWeight   = $ci->query()
                ->from('field')
                ->where('field_name', 'weight')
                ->fetch()[ 'field_id' ];

        $ci->query()
            ->insertInto('node_type', [
                'node_type', 'node_type_name', 'node_type_description'
            ])
            ->values([
                'node_type'             => 'page_faq',
                'node_type_name'        => 'FAQ',
                'node_type_description' => 'Create your question and answer page'
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

    public function hookInstall(ContainerInterface $ci)
    {
    }

    public function uninstall(ContainerInterface $ci)
    {
        $ci->query()->from('node')
            ->delete()
            ->where('type', 'page_faq')
            ->execute();
        $ci->query()->from('node_type_field')
            ->delete()
            ->where('node_type', 'page_faq')
            ->orWhere('node_type', 'faq')
            ->execute();
        $ci->query()->from('node_type')
            ->delete()
            ->where('node_type', 'page_faq')
            ->execute();
        $ci->query()->from('field')
            ->delete()
            ->where('field_name', 'question')
            ->orWhere('field_name', 'response')
            ->orWhere('field_name', 'faq')
            ->execute();
        $ci->schema()->dropTable('entity_page_faq');
        $ci->schema()->dropTable('entity_faq');
    }

    public function hookUninstall(ContainerInterface $ci)
    {
    }

    public function hookInstallNode(ContainerInterface $ci)
    {
    }
}
