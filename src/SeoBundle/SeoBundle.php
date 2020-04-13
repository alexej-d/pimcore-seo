<?php

namespace SeoBundle;

use SeoBundle\DependencyInjection\Compiler\MetaDataExtractorPass;
use SeoBundle\DependencyInjection\Compiler\MetaDataIntegratorPass;
use SeoBundle\Tool\Install;
use SeoBundle\DependencyInjection\Compiler\ResourceProcessorPass;
use SeoBundle\DependencyInjection\Compiler\IndexWorkerPass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

class SeoBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'dachcom-digital/seo';

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Install::class);
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->configureDoctrineExtension($container);

        $container->addCompilerPass(new IndexWorkerPass());
        $container->addCompilerPass(new ResourceProcessorPass());
        $container->addCompilerPass(new MetaDataExtractorPass());
        $container->addCompilerPass(new MetaDataIntegratorPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function configureDoctrineExtension(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createYamlMappingDriver(
                [$this->getNameSpacePath() => $this->getNamespaceName()],
                ['seo.persistence.doctrine.manager'],
                'seo.persistence.doctrine.enabled'
            )
        );
    }

    /**
     * @return array
     */
    public function getCssPaths()
    {
        return [
            '/bundles/seo/css/admin.css',
            '/bundles/seo/css/integrator/title-description.css'
        ];
    }

    /**
     * @return string[]
     */
    public function getJsPaths()
    {
        return [
            '/bundles/seo/js/plugin.js',
            '/bundles/seo/js/metaData/abstractMetaDataPanel.js',
            '/bundles/seo/js/metaData/documentMetaDataPanel.js',
            '/bundles/seo/js/metaData/objectMetaDataPanel.js',
            '/bundles/seo/js/metaData/integrator/abstractIntegrator.js',
            '/bundles/seo/js/metaData/integrator/titleDescriptionIntegrator.js',
        ];
    }

    /**
     * @return string|null
     */
    protected function getNamespaceName()
    {
        return 'SeoBundle\Model';
    }

    /**
     * @return string
     */
    protected function getNameSpacePath()
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            'model'
        );
    }
}
