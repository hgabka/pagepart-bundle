<?php

namespace Hgabka\PagePartBundle\PagePartConfigurationReader;

use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminConfigurator;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class PagePartConfigurationParser implements PagePartConfigurationParserInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    private $presets = [];

    private $stack = [];
    
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
     * @param string                               $name
     * @param PagePartAdminConfiguratorInterface[] $existing
     *
     * @throws \Exception
     *
     * @return PagePartAdminConfiguratorInterface
     */
    public function parse($name, array $existing = [])
    {
        if (\in_array($name, $this->stack, true)) {
            throw new \RuntimeException(sprintf('Recursion detected when parsing %s -> %s', implode(' -> ', $this->stack), $name));
        }
        $this->stack[] = $name;

        $value = $this->getValue($name);

        if (!\array_key_exists('types', $value)) {
            $value['types'] = [];
        }

        if (\array_key_exists('extends', $value)) {
            $namespace = '';
            if (false !== strpos($name, ':')) {
                $namespace = substr($name, 0, strpos($name, ':') + 1);
            }

            foreach ((array) $value['extends'] as $extend) {
                if (false === strpos($extend, ':')) {
                    $extend = $namespace . $extend;
                }

                if (false === isset($existing[$extend])) {
                    $existing[$extend] = $this->parse($extend, $existing);
                }

                $value['types'] = array_merge($existing[$extend]->getPossiblePagePartTypes(), $value['types']);
            }
        }

        $types = [];
        foreach ($value['types'] as $type) {
            if ('' === (string) $type['class']) {
                unset($types[$type['name']]);

                continue;
            }

            $types[$type['name']] = ['name' => $type['name'], 'class' => $type['class'], 'preview' => \array_key_exists('preview', $type) ? $type['preview'] : ''];
            if (isset($type['pagelimit'])) {
                $types[$type['name']]['pagelimit'] = $type['pagelimit'];
            }
        }

        $result = new PagePartAdminConfigurator();
        $result->setName($value['name']);
        $result->setInternalName($name);
        $result->setPossiblePagePartTypes(array_values($types));
        $result->setContext($value['context']);
        $result->setConfig($value['config'] ?? null);

        if (isset($value['widget_template'])) {
            $result->setWidgetTemplate($value['widget_template']);
        }

        array_pop($this->stack);

        return $result;
    }

    private function getValue($name)
    {
        if (isset($this->presets[$name])) {
            return $this->presets[$name];
        }

        $nameParts = explode(':', $name);
        if (2 !== \count($nameParts)) {
            $path = $this->kernel->getProjectDir() . '/config/pageparts/' . $name . '.yml';
        } else {
            [$namespace, $name] = $nameParts;
            $path = $this->kernel->locateResource('@' . $namespace . '/Resources/config/pageparts/' . $name . '.yml');
        }
        $value = Yaml::parse(file_get_contents($path));

        return $value;
    }
}
