{
    "openapi": "3.0.1",
    "info": {
        "title": "{{$configuration['group'] . " " . $configuration['service']}}",
        "version": "1.0.0",
        "contact": {
            "email": "metaapi@metaapi.com",
            "name": "metaAPI Team"
        },
        "license": {
            "name": "Apache 2.0",
            "url": "http://www.apache.org/licenses/LICENSE-2.0.html"
        }
    },
    "servers": [
        {
        "url": "http://127.0.0.1:8000/meta-api/services/{{$configuration['group']}}/{{$configuration['service']}}",
        "variables": {},
        "description": "Main server"
        }
    ],
    "paths": {
@foreach($configuration['operations'] as $operation_key => $operation)
    "/{{$operation_key}}": {
    @if(isset($operation['params']))
        "parameters": [@foreach($operation['params'] as $param_key => $param)
                {
                    "in": "query",
                    "name": "{{$param_key}}",
                    @if(isset($param['description']))
                    "description": "{{$param['description']}}",
                    @endif
                    "schema": {
                        "type": "{{$param['type']}}"
                        @if(key_exists('default',$param))
                        ,"default": "{{$param['default']}}"
                        @endif
                    },
                    "required": {{json_encode($param['required'])}}
                }{{$loop->last ? '' : ','}}
            @endforeach],
    @endif
    "get": {
        "summary": "{{$operation['description']}}",
        "description": "",
        "operationId": "{{$operation_key}}",
        "responses": {
            "200": {
                "description": "OK",
                "content": {
                    "application/json": {
                    }
                }
            }
        }
    }
    }{{$loop->last ? '' : ','}}
@endforeach
},
    "security": [
            {
                "bearerAuth": [""]
            }
        ],
    "components": {
        "securitySchemes": {
            "bearerAuth": {
                "type": "http",
                "scheme": "bearer"
                }
            }
        }
}
