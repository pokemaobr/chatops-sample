<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use DigitalOceanV2\Adapter\GuzzleAdapter;
use DigitalOceanV2\DigitalOceanV2;

// create an adapter with your access token which can be
// generated at https://cloud.digitalocean.com/settings/applications
$adapter = new GuzzleAdapter($digitalOceanKey);

// create a digital ocean object with the previous adapter
$digitalocean = new DigitalOceanV2($adapter);

$droplet = $digitalocean->droplet();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
        $requestBody = file_get_contents('php://input');
        $json = json_decode($requestBody);

        $intent = $json->result->metadata->intentName;

        switch ($intent) {
            case 'listar': {
                $droplets = $droplet->getAll();

                $i = 0;
                foreach ($droplets as $drop) {

                    $newDrops[$i]['name'] = $drop->name;
                    $newDrops[$i]['region'] = $drop->region->name;
                    $newDrops[$i]['memory'] = $drop->memory;
                    $newDrops[$i]['disk'] = $drop->disk;
                    $i++;
                }
                $i = 1;
  
                $speech = 'Você tem '.count($newDrops). ' droplets. '. ' Seguem as configurações: ';
                foreach ($newDrops as $dropx) {
                        $speech .= ' Droplet ' . $i;
                        $speech .= ' nome = ' . $dropx['name'];
                        $speech .= ' região = ' . $dropx['region'];
                        $speech .= ' memória = ' . $dropx['memory'];
                        $speech .= ' espaço em disco = ' . $dropx['disk'];
                        $i++;
                }
            }
            break;

            case 'criar': {
                $name = $json->result->parameters->nome;
                $created = $droplet->create($name, 'nyc3', '512mb', 'ubuntu-14-04-x64');
                $speech = 'Acabamos de criar para você o droplet ' . $created->name ;
                $speech .= ' ele foi criando com o id: ' . $created->id . ' possui ';
                $speech .= $created->memory . ' MB de memória e um espaço em disco de ';
                $speech .= $created->disk . 'GB.';
            }
            break;

            case 'deletar': {
                $id = $json->result->parameters->id;
                $deleted = $droplet->delete($id);
                $speech = 'Droplet de id ' . $id . ' devidamente deletado';
            }
            break;

            default:
                $speech = 'nenhuma intenção corresponde a sua frase.';
            break;
            }
    
            $response = new \stdClass();
            $response->speech = $speech;
            $response->displayText = $speech;
            $response->source = 'webhook';
            echo json_encode($response);
    
    
    }
    else
    {
            echo 'Metodo não aceito';
    
    }
        
