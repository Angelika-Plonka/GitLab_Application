<?php

namespace AppBundle\Service;

class LoadedGitLabApi
{

    public function loadGitLabData($projects): array
    {
        $data = [];
        foreach ($projects as $project) {
            if (isset($project['name']) && isset($project['id'])) {
                $data[$project['id']] = [
                    'id' => $project['id'],
                    'name' => $project['name'],
                    'namespace' => $project['namespace']
                ];
            }
        }

        return $data;
    }


    public function loadNamespaces($namespaces): array
    {
        $namespacesMap = [];
        foreach($namespaces as $namespace){
            if(isset($namespace['id'])){
                $namespacesMap[$namespace['id']] = [
                    'id' => $namespace['id'],
                    'name' => $namespace['name']
                ];
            }
        }
        return $namespacesMap;
    }


}
