<?php

namespace ScoutElastic\Tests\Dependencies;

use ScoutElastic\IndexConfigurator as ElasticIndexConfigurator;

trait IndexConfigurator
{
    /**
     * @param  array  $params Available parameters: name, settings, default_mapping, methods.
     * @return ElasticIndexConfigurator
     */
    public function mockIndexConfigurator(array $params = [])
    {
        $name = $params['name'] ?? 'test';

        $methods = array_merge($params['methods'] ?? [], [
            'getName',
            'getSettings',
            'getDefaultMapping',
            'getWriteAlias',
        ]);

        $mock = $this->buildPartialMock(ElasticIndexConfigurator::class, $methods);

        $mock->method('getName')
             ->willReturn($name);

        $mock->method('getSettings')
             ->willReturn($params['settings'] ?? []);

        $mock->method('getDefaultMapping')
             ->willReturn($params['default_mapping'] ?? []);

        $mock->method('getWriteAlias')
             ->willReturn($name.'_write');

        return $mock;
    }

    /**
     * @param  string  $class
     * @param  array  $methods
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function buildPartialMock($class, array $methods)
    {
        $existingMethods = array_values(array_filter($methods, function ($method) use ($class) {
            return method_exists($class, $method);
        }));

        $missingMethods = array_values(array_diff($methods, $existingMethods));

        $builder = $this->getMockBuilder($class);

        if ($existingMethods) {
            $builder->onlyMethods($existingMethods);
        }

        if ($missingMethods) {
            $builder->addMethods($missingMethods);
        }

        return $builder->getMock();
    }
}
