<?php

namespace Meduza\Plugin;

use Meduza\Build\Build;
use Meduza\Content\Content;

class Categorize extends PluginBase
{
    public function __construct(Build $build)
    {
        parent::__construct($build);
    }
    
    public function run(): void
    {
        $config = $this->build->config()->getConfig();
        $content = $this->build->getContent()->getIterator();
        $hierarchy = [];

        foreach ($content as $item) {
            $frontmatter = $item->frontmatter()->getFrontmatter();
            if(!key_exists($config['plugins']['Categorize']['lookFor'], $frontmatter)) continue;
            $hierarchy = array_merge_recursive($hierarchy, $this->process($item, $frontmatter[$config['plugins']['Categorize']['lookFor']]));
        }
        $this->build->setPluginData('categorizer', $hierarchy);
    }

    protected function process(Content $content, array $categories): array
    {
        $category = array_shift($categories);

        if (sizeof($categories) > 0) {
            $nodes = $this->process($content, $categories);
        } else {
            $nodes = [];
        }

        $current[$category]['content'] = $content;
        $current[$category]['nodes'] = $nodes;

        return $current;
    }
}
