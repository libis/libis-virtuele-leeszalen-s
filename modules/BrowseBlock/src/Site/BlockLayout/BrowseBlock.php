<?php
namespace BrowseBlock\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\View\Renderer\PhpRenderer;

class BrowseBlock extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'Libis - Browse preview'; // @translate
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
        SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {
        $defaults = [
            'resource_type' => 'items',
            'query' => '',
            'heading' => '',
            'columns' => '2',
            'text' => '',
            'limit' => 12,
            'link' => '',
            'link-text' => 'Browse all', // @translate
        ];

        $data = $block ? $block->data() + $defaults : $defaults;

        $form = new Form();
        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][heading]',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Preview title', // @translate
                'info' => 'Heading above resource list, if any.', // @translate
            ],
        ]);
        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][text]',
            'type' => Element\Textarea::class,
            'attributes' => ['class' => 'block-html full wysiwyg',],            
            'options' => [
                'label' => 'Description', // @translate
                'info' => 'Description that appears above the items'
            ],
        ]);
        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][resource_type]',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Resource type', // @translate
                'value_options' => [
                    'items' => 'Items',  // @translate
                    'item_sets' => 'Item sets',  // @translate
                    'media' => 'Media',  // @translate
                ],
            ],
        ]);
        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][columns]',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Number of columns', // @translate
                'value_options' => [
                    '6' => '2',  // @translate
                    '4' => '3',  // @translate
                ],
            ],
        ]);
        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][query]',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Query', // @translate
                'info' => 'Display resources using this search query', // @translate
                'documentation' => 'https://omeka.org/s/docs/user-manual/sites/site_pages/#browse-preview',
            ],
        ]);
        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][limit]',
            'type' => Element\Number::class,
            'options' => [
                'label' => 'Limit', // @translate
                'info' => 'Maximum number of resources to display in the preview.', // @translate
            ],
        ]);

        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][link]',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Link', // @translate
                'info' => 'Link to full browse view.', // @translate
            ],
        ]);
        $form->add([
            'name' => 'o:block[__blockIndex__][o:data][link-text]',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Link text', // @translate
                'info' => 'Text for link to full browse view, if any.', // @translate
            ],
        ]);

        $form->setData([
            'o:block[__blockIndex__][o:data][resource_type]' => $data['resource_type'],
            'o:block[__blockIndex__][o:data][query]' => $data['query'],
            'o:block[__blockIndex__][o:data][columns]' => $data['columns'],
            'o:block[__blockIndex__][o:data][text]' => $data['text'],
            'o:block[__blockIndex__][o:data][heading]' => $data['heading'],
            'o:block[__blockIndex__][o:data][limit]' => $data['limit'],
            'o:block[__blockIndex__][o:data][link]' => $data['link'],
            'o:block[__blockIndex__][o:data][link-text]' => $data['link-text'],
        ]);

        return $view->formCollection($form);
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $resourceType = $block->dataValue('resource_type', 'items');
        $limit = $query['limit'] = $block->dataValue('limit', 12);

        parse_str($block->dataValue('query'), $query);
        $originalQuery = $query;

        $site = $block->page()->site();
        if ($view->siteSetting('browse_attached_items', false)) {
            //$query['site_attachments_only'] = true;
        }

        $query['site_id'] = $site->id();
        $query['limit'] = $block->dataValue('limit', 12);

        if (!isset($query['sort_by'])) {
            $query['sort_by'] = 'created';
        }
        if (!isset($query['sort_order'])) {
            $query['sort_order'] = 'desc';
        }

        //var_dump($resourceType);
        $response = $view->api()->search($resourceType, $query);

        $resources = $response->getContent();
        $resources = array_slice($resources,0,$limit);
        //var_dump($resources);
        $resourceTypes = [
            'items' => 'item',
            'item_sets' => 'item-set',
            'media' => 'media',
        ];

        return $view->partial('common/block-layout/browse-block', [
            'block' => $block,
            'resourceType' => $resourceTypes[$resourceType],
            'resources' => $resources,
            'heading' => $block->dataValue('heading'),
            'link' => $block->dataValue('link'),
            'linkText' => $block->dataValue('link-text'),
            'query' => $originalQuery,
        ]);
    }
}
