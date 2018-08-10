<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use SebastiaanLuca\AutoMorphMap\Constants\CaseTypes;
use SebastiaanLuca\AutoMorphMap\Constants\NamingSchemes;
use Symfony\Component\Finder\Finder;

class Mapper
{
    /**
     * Scan all model directories and automatically alias the polymorphic types of Eloquent models.
     *
     * @return void
     */
    public function map() : void
    {
        if ($this->useCache()) {
            return;
        }

        $models = $this->getModels();

        $map = $this->getModelMap($models);

        $this->mapModels($map);
    }

    /**
     * @return string
     */
    public function getCachePath() : string
    {
        return base_path('bootstrap/cache/morphmap.php');
    }

    /**
     * @return array
     */
    public function getModels() : array
    {
        $config = $this->getComposerConfig();

        $paths = $this->getModelPaths($config);

        if (empty($paths)) {
            return [];
        }

        return $this->scan($paths);
    }

    /**
     * @param array $models
     *
     * @return array
     */
    public function getModelMap(array $models) : array
    {
        $map = [];

        foreach ($models as $model) {
            array_set($map, $this->getModelAlias($model), $model);
        }

        return $map;
    }

    /**
     * @return bool
     */
    protected function useCache() : bool
    {
        if (! file_exists($cache = $this->getCachePath())) {
            return false;
        }

        $this->mapModels(require $cache);

        return true;
    }

    /**
     * @return array
     */
    protected function getComposerConfig() : array
    {
        $composer = file_get_contents(base_path('composer.json'));

        return json_decode($composer, true, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function getModelPaths(array $config) : array
    {
        $paths = array_get($config, 'autoload.psr-4');

        $paths = collect($paths)
            ->unique()
            ->mapWithKeys(function (string $path, string $namespace) {
                return [$namespace => base_path(rtrim($path, '/'))];
            })
            ->filter(function (string $path) {
                return is_dir($path);
            });

        return $paths->toArray();
    }

    /**
     * @param array $paths
     *
     * @return array
     */
    protected function scan(array $paths) : array
    {
        $models = [];

        foreach ($paths as $namespace => $path) {
            foreach ((new Finder)->in($path)->files() as $file) {
                $model = $namespace . str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        str_after($file->getPathname(), $path . DIRECTORY_SEPARATOR)
                    );

                if (! class_exists($model)) {
                    continue;
                }

                $reflection = new ReflectionClass($model);

                if ($reflection->isAbstract() || ! is_subclass_of($model, Model::class)) {
                    continue;
                }

                $models[] = $model;
            }
        }

        return $models;
    }

    /**
     * @param array $map
     */
    protected function mapModels(array $map) : void
    {
        $existing = Relation::morphMap() ?: [];

        if (! empty($existing)) {
            $map = collect($map)
                ->reject(function (string $class, string $alias) use ($existing) {
                    return array_key_exists($alias, $existing) || in_array($class, $existing);
                })
                ->toArray();
        }

        Relation::morphMap($map);
    }

    /**
     * @param string $model
     *
     * @return string
     */
    protected function getModelAlias(string $model) : string
    {
        $callback = config('auto-morph-map.conversion');

        if ($callback && is_callable($callback)) {
            return $callback($model);
        }

        $name = $this->getModelName($model);

        switch (config('auto-morph-map.case')) {
            case CaseTypes::SNAKE_CASE:
                return snake_case($name);

            case CaseTypes::SLUG_CASE:
                return str_slug($name);

            case CaseTypes::CAMEL_CASE:
                return camel_case($name);

            case CaseTypes::STUDLY_CASE:
                return studly_case($name);

            case CaseTypes::NONE:
            default:
                return $name;
        }
    }

    /**
     * @param string $model
     *
     * @return string
     */
    private function getModelName(string $model) : string
    {
        switch (config('auto-morph-map.naming')) {
            case NamingSchemes::TABLE_NAME:
                return app($model)->getTable();

            case NamingSchemes::CLASS_BASENAME:
                return class_basename($model);

            case NamingSchemes::SINGULAR_TABLE_NAME:
            default:
                return str_singular(
                    app($model)->getTable()
                );
        }
    }
}
