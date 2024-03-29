<?php

namespace Hgabka\PagePartBundle\PageTemplate;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class PageTemplateConfigurationParser implements PageTemplateConfigurationParserInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    private $presets = [];

    /** @var HgabkaUtils */
    private $utils;

    /**
     * @param KernelInterface $kernel
     * @param array           $presets
     */
    public function __construct(KernelInterface $kernel, HgabkaUtils $utils, array $presets = [])
    {
        $this->kernel = $kernel;
        $this->utils = $utils;
        $this->presets = $presets;
    }

    /**
     * This will read the $name file and parse it to the PageTemplate.
     *
     * @param string $name
     *
     * @throws \Exception
     *
     * @return PageTemplateInterface
     */
    public function parse($name)
    {
        $rawData = $this->getRawData($name);

        $result = new PageTemplate();
        $result->setName($rawData['name']);

        $rows = [];
        foreach ($rawData['rows'] as $rawRow) {
            $regions = [];
            foreach ($rawRow['regions'] as $rawRegion) {
                $region = $this->buildRegion($rawRegion);
                $regions[] = $region;
            }
            $rows[] = new Row($regions);
        }

        $result->setRows($rows);
        $result->setTemplate($rawData['template']);

        return $result;
    }

    /**
     * This builds a Region out of the rawRegion from the Yaml.
     *
     * @param array $rawRegion
     *
     * @return Region
     */
    private function buildRegion($rawRegion)
    {
        $children = [];
        $rows = [];
        $rawRegion = array_replace(['regions' => [], 'rows' => []], $rawRegion);

        foreach ($rawRegion['regions'] as $child) {
            $children[] = $this->buildRegion($child);
        }

        foreach ($rawRegion['rows'] as $row) {
            $rows[] = $this->buildRow($row);
        }

        $rawRegion = array_replace([
            'name' => null,
            'span' => 12,
            'template' => null,
        ], $rawRegion);

        return new Region($rawRegion['name'], $rawRegion['span'], $rawRegion['template'], $children, $rows);
    }

    /**
     * This builds a Row out of the rawRow from the Yaml.
     *
     * @param array $rawRow
     *
     * @return Row
     */
    private function buildRow($rawRow)
    {
        $regions = [];

        foreach ($rawRow as $region) {
            $regions[] = $this->buildRegion($region);
        }

        return new Row($regions);
    }

    /**
     * @param $name
     *
     * @throws \Exception
     *
     * @return array
     */
    private function getRawData($name)
    {
        if (isset($this->presets[$name])) {
            return $this->presets[$name];
        }

        if (false === strpos($name, ':')) {
            $path = $this->kernel->getProjectDir() . '/config/pagetemplates/' . $name . '.yaml';
        } else {
            [$namespace, $name] = explode(':', $name, 2);
            $path = $this->kernel->locateResource('@' . $namespace . '/Resources/config/pagetemplates/' . $name . '.yaml');
        }
        $contents = @file_get_contents($path);

        if (false === $contents) {
            $path = $this->utils->replaceExtension($path, 'yml');
            $contents = file_get_contents($path);
        }

        return Yaml::parse($contents);
    }
}
