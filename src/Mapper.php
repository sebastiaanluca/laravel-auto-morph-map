<?php

declare(strict_types=1);

namespace SebastiaanLuca\AutoMorphMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
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

        $this->mapModels($models);
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
     * @return string
     */
    public function getCachePath() : string
    {
        return base_path('bootstrap/cache/morphmap.php');
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
     * @param array $models
     */
    protected function mapModels(array $models) : void
    {
        $existing = Relation::morphMap();

        $map = [];

        foreach ($models as $model) {
            array_set($map, $this->getModelAlias($model), $model);
        }

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
        $basename = class_basename($model);

        switch (config('auto-morph-map.case')) {
            case CaseTypes::CAMEL_CASE:
                return camel_case($basename);

            case CaseTypes::STUDLY_CASE:
                return studly_case($basename);

            case CaseTypes::SNAKE_CASE:
            default:
                return snake_case($basename);
        }
    }
}
