<?php


// NOT BEING USED....................
public function doParameters($module,$route) {
        
    $expectedRouteParams = $route->getParameters();
    $urlNamedParameters = $this->request->getUrlNamedParameters();
    $args = $this->request->getArguments();
    $namedParamKeys = array_keys($urlNamedParameters);
    $params = array();

    //if the parameter is defined by name then use the value for that name otherwise use the value at the current index
    //Determine which kind of paramter to give preference to.
    if(!empty($urlNamedParameters) && empty($args)){

        for($i = 0; $i < count($expectedRouteParams); $i++){

            if(in_array($namedParamKeys[$i],$expectedRouteParams)){

                $params[] = $urlNamedParameters[$namedParamKeys[$i]];
            }

            if(count($params) == 0){

                $params = $args;
            }
        }
    } else {

        $params = $args;
    }
}